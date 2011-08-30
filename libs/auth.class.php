<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Authentication library.
     *
     * @octdoc      c:core/auth
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class auth
    /**/
    {
        /**
         * Authentication status codes.
         *
         * @octdoc  d:auth/T_AUTH_SUCCESS, T_AUTH_FAILURE, T_IDENTITY_UNKNOWN, T_IDENTITY_AMBIGUOUS, T_CREDENTIAL_INVALID
         */
        const T_AUTH_SUCCESS       = 1;
        const T_AUTH_FAILURE       = 0;
        const T_IDENTITY_UNKNOWN   = -1;
        const T_IDENTITY_AMBIGUOUS = -2;
        const T_CREDENTIAL_INVALID = -3;
        /**/

        /**
         * Instance of auth class.
         *
         * @octdoc  v:auth/$instance
         * @var     \org\octris\core\auth
         */
        private static $instance = null;
        /**/

        /**
         * Authentication storage handler.
         *
         * @octdoc  v:auth/$storage
         * @var     \org\octris\core\auth\storage_if
         */
        protected $storage;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:auth/__construct
         */
        protected function __construct()
        /**/
        {
            $this->storage = new \org\octris\core\auth\storage\transient();
        }

        /*
         * prevent cloning
         */
        private function __clone() {}

        /**
         * Return instance of auth class, implemented as singleton-pattern.
         *
         * @octdoc  m:auth/getInstance
         * @return  \org\octris\core\auth
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }

            return self::$instance;
        }

        /**
         * Sets the storage handler for authentication information.
         *
         * @octdoc  m:auth/setStorage
         * @param   \org\octris\core\auth\storage_if    $storage    Instance of storage backend.
         */
        public function setStorage(\org\octris\core\auth\storage_if $storage)
        /**/
        {
            $this->storage = $storage;
        }

        /**
         * Test whether there is already an identity authenticated.
         *
         * @octdoc  m:authenticate/isAuthenticated
         * @return  bool                                            Returns true, if an identity is authenticated.
         */
        public function isAuthenticated()
        /**/
        {
            if (($return = (!$this->storage->isEmpty()))) {
                $identity = $this->storage->getIdentity();

                $return = $identity->isValid();
            }

            return $return;
        }

        /**
         * Authenticate againat the specified authentication adapter.
         *
         * @octdoc  m:auth/authenticate
         * @param   \org\octris\core\auth\adapter_if    $adapter    Instance of adapter to use for authentication.
         * @return  \org\octris\core\auth\identity                  The authenticated identity.
         */
        public function authenticate(\org\octris\core\auth\adapter_if $adapter)
        /**/
        {
            $identity = $adapter->authenticate();

            $this->storage->setIdentity($identity);

            return $identity;
        }

        /**
         * Remove identity so it is no longer authenticated.
         *
         * @octdoc  m:auth/revokeIdentity
         */
        public function revokeIdentity()
        /**/
        {
            if (!$this->storage->isEmpty()) {
                $this->storage->unsetIdentity();
            }
        }
    }
}
