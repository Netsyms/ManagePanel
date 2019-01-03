<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use Base32\Base32;
use OTPHP\TOTP;

class User {

    private $uid = null;
    private $username;
    private $passhash;
    private $email;
    private $realname;
    private $authsecret;
    private $has2fa = false;
    private $exists = false;

    public function __construct(int $uid, string $username = "") {
        global $database;
        if ($database->has('accounts', ['AND' => ['uid' => $uid, 'deleted' => false]])) {
            $this->uid = $uid;
            $user = $database->get('accounts', ['username', 'password', 'email', 'realname', 'authsecret'], ['uid' => $uid]);
            $this->username = $user['username'];
            $this->passhash = $user['password'];
            $this->email = $user['email'];
            $this->realname = $user['realname'];
            $this->authsecret = $user['authsecret'];
            $this->has2fa = !empty($user['authsecret']);
            $this->exists = true;
        } else {
            $this->uid = $uid;
            $this->username = $username;
        }
    }

    public static function byUsername(string $username): User {
        global $database;
        $username = strtolower($username);
        if ($database->has('accounts', ['AND' => ['username' => $username, 'deleted' => false]])) {
            $uid = $database->get('accounts', 'uid', ['username' => $username]);
            return new self($uid * 1);
        }
        return new self(-1, $username);
    }

    /**
     * Add a user to the system.  /!\ Assumes input is OK /!\
     * @param string $username Username, saved in lowercase.
     * @param string $password Password, will be hashed before saving.
     * @param string $realname User's real legal name
     * @param string $email User's email address.
     * @param string $phone1 Phone number #1
     * @param string $phone2 Phone number #2
     * @param int $type Account type
     * @return int The new user's ID number in the database.
     */
    public static function add(string $username, string $password, string $realname, string $email = null, string $phone1 = "", string $phone2 = "", int $type = 1): int {
        global $database;
        $database->insert('accounts', [
            'username' => strtolower($username),
            'password' => (is_null($password) ? null : password_hash($password, PASSWORD_BCRYPT)),
            'realname' => $realname,
            'email' => $email,
            'phone1' => $phone1,
            'phone2' => $phone2,
            'acctstatus' => 1,
            'accttype' => $type
        ]);
        return $database->id();
    }

    public function exists(): bool {
        return $this->exists;
    }

    public function has2fa(): bool {
        return $this->has2fa;
    }

    function getUsername() {
        return $this->username;
    }

    function getUID() {
        return $this->uid;
    }

    function getEmail() {
        return $this->email;
    }

    function getName() {
        return $this->realname;
    }

    /**
     * Check the given plaintext password against the stored hash.
     * @param string $password
     * @return bool
     */
    function checkPassword(string $password): bool {
        return password_verify($password, $this->passhash);
    }

    /**
     * Change the user's password.
     * @global $database $database
     * @param string $old The current password
     * @param string $new The new password
     * @param string $new2 New password again
     * @throws PasswordMatchException
     * @throws PasswordMismatchException
     * @throws IncorrectPasswordException
     * @throws WeakPasswordException
     */
    function changePassword(string $old, string $new, string $new2) {
        global $database, $SETTINGS;
        if ($old == $new) {
            throw new PasswordMatchException();
        }
        if ($new != $new2) {
            throw new PasswordMismatchException();
        }

        if (!$this->checkPassword($old)) {
            throw new IncorrectPasswordException();
        }

        require_once __DIR__ . "/worst_passwords.php";

        $passrank = checkWorst500List($new);
        if ($passrank !== FALSE) {
            throw new WeakPasswordException();
        }
        if (strlen($new) < $SETTINGS['min_password_length']) {
            throw new WeakPasswordException();
        }

        $database->update('accounts', ['password' => password_hash($new, PASSWORD_DEFAULT), 'acctstatus' => 1], ['uid' => $this->uid]);
        Log::insert(LogType::PASSWORD_CHANGED, $this);
        return true;
    }

    function check2fa(string $code): bool {
        if (!$this->has2fa) {
            return true;
        }

        $totp = new TOTP(null, $this->authsecret);
        $time = time();
        if ($totp->verify($code, $time)) {
            return true;
        }
        if ($totp->verify($code, $time - 30)) {
            return true;
        }
        if ($totp->verify($code, $time + 30)) {
            return true;
        }

        return false;
    }

    /**
     * Generate a TOTP secret for the given user.
     * @return string OTP provisioning URI (for generating a QR code)
     */
    function generate2fa(): string {
        global $SETTINGS;
        $secret = random_bytes(20);
        $encoded_secret = Base32::encode($secret);
        $totp = new TOTP((empty($this->email) ? $this->realname : $this->email), $encoded_secret);
        $totp->setIssuer($SETTINGS['system_name']);
        return $totp->getProvisioningUri();
    }

    /**
     * Save a TOTP secret for the user.
     * @global $database $database
     * @param string $username
     * @param string $secret
     */
    function save2fa(string $secret) {
        global $database;
        $database->update('accounts', ['authsecret' => $secret], ['username' => $this->username]);
    }

    /**
     * Check if the given username has the given permission (or admin access)
     * @global $database $database
     * @param string $code
     * @return boolean TRUE if the user has the permission (or admin access), else FALSE
     */
    function hasPermission(string $code): bool {
        global $database;
        return $database->has('assigned_permissions', [
                    '[>]permissions' => [
                        'permid' => 'permid'
                    ]
                        ], ['AND' => ['OR' => ['permcode #code' => $code, 'permcode #admin' => 'ADMIN'], 'uid' => $this->uid]]) === TRUE;
    }

    /**
     * Get the account status.
     * @return \AccountStatus
     */
    function getStatus(): AccountStatus {
        global $database;
        $statuscode = $database->get('accounts', 'acctstatus', ['uid' => $this->uid]);
        return new AccountStatus($statuscode);
    }

    function sendAlertEmail(string $appname = null) {
        global $SETTINGS;
        if (is_null($appname)) {
            $appname = $SETTINGS['site_title'];
        }
        if (empty(ADMIN_EMAIL) || filter_var(ADMIN_EMAIL, FILTER_VALIDATE_EMAIL) === FALSE) {
            return "invalid_to_email";
        }
        if (empty(FROM_EMAIL) || filter_var(FROM_EMAIL, FILTER_VALIDATE_EMAIL) === FALSE) {
            return "invalid_from_email";
        }

        $mail = new PHPMailer;

        if ($SETTINGS['debug']) {
            $mail->SMTPDebug = 2;
        }

        if ($SETTINGS['email']['use_smtp']) {
            $mail->isSMTP();
            $mail->Host = $SETTINGS['email']['host'];
            $mail->SMTPAuth = $SETTINGS['email']['auth'];
            $mail->Username = $SETTINGS['email']['user'];
            $mail->Password = $SETTINGS['email']['password'];
            $mail->SMTPSecure = $SETTINGS['email']['secure'];
            $mail->Port = $SETTINGS['email']['port'];
            if ($SETTINGS['email']['allow_invalid_certificate']) {
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            }
        }

        $mail->setFrom(FROM_EMAIL, 'Account Alerts');
        $mail->addAddress(ADMIN_EMAIL, "System Admin");
        $mail->isHTML(false);
        $mail->Subject = $Strings->get("admin alert email subject", false);
        $mail->Body = $Strings->build("admin alert email message", ["username" => $this->username, "datetime" => date("Y-m-d H:i:s"), "ipaddr" => IPUtils::getClientIP(), "appname" => $appname], false);

        if (!$mail->send()) {
            return $mail->ErrorInfo;
        }
        return true;
    }

}

class AccountStatus {

    const NORMAL = 1;
    const LOCKED_OR_DISABLED = 2;
    const CHANGE_PASSWORD = 3;
    const TERMINATED = 4;
    const ALERT_ON_ACCESS = 5;

    private $status;

    public function __construct(int $status) {
        $this->status = $status;
    }

    /**
     * Get the account status/state as an integer.
     * @return int
     */
    public function get(): int {
        return $this->status;
    }

    /**
     * Get the account status/state as a string representation.
     * @return string
     */
    public function getString(): string {
        switch ($this->status) {
            case self::NORMAL:
                return "NORMAL";
            case self::LOCKED_OR_DISABLED:
                return "LOCKED_OR_DISABLED";
            case self::CHANGE_PASSWORD:
                return "CHANGE_PASSWORD";
            case self::TERMINATED:
                return "TERMINATED";
            case self::ALERT_ON_ACCESS:
                return "ALERT_ON_ACCESS";
            default:
                return "OTHER_" . $this->status;
        }
    }

}
