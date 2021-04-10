<?php
if (!class_exists('VultrAPI2')) {
    /**
     * Vultr.com API Client
     * @package vultr
     * @version 2.0
     * @author  https://github.com/whattheserver/whmcs-vultr
     * @license http://www.opensource.org/licenses/mit-license.php MIT
     * @see     https://github.com/whattheserver/whmcs-vultr/
     */
    class VultrAPI2
    {

        /**
         * API Token
         * @access private
         * @type string $api_token Vultr.com API token
         * @see https://my.vultr.com/settings/
         */
        private $api_token = '';

        /**
         * API Endpoint
         * @access public
         * @type string URL for Vultr.com API
         */
        public $endpoint = 'https://api.vultr.com/v2/';

        /**
         * Current Version
         * @access public
         * @type string Current version number
         */
        public $version = '2.0';

        /**
         * User Agent
         * @access public
         * @type string API User-Agent string
         */
        public $agent = 'Vultr.com API Client';

        /**
         * Debug Variable
         * @access public
         * @type bool Debug API requests
         */
        public $debug = false;

        /**
         * Snapshots Variable
         * @access public
         * @type mixed Array to store snapshot IDs
         */
        public $snapshots = array();

        /**
         * Plans Variable
         * @access public
         * @type mixed Array to store VPS Plan IDs
         */
        public $plans = array();

        /**
         * Regions Variable
         * @access public
         * @type mixed Array to store available regions
         */
        public $regions = array();

        /**
         * Scripts Variable
         * @access public
         * @type mixed Array to store startup scripts
         */
        public $scripts = array();

        /**
         * Servers Variable
         * @access public
         * @type mixed Array to store server data
         */
        public $servers = array();

        /**
         * Account Variable
         * @access public
         * @type mixed Array to store account data
         */
        public $account = array();

        /**
         * OS List Variable
         * @access public
         * @type mixed Array to store OS list
         */
        public $oses = array();

        /**
         * SSH Keys variable
         * @access public
         * @type mixed Array to store SSH keys
         * */
        public $ssh_keys = array();

        /**
         * Response code variable
         * @access public
         * @type int Holds HTTP response code from API
         * */
        public $response_code = 0;

        /**
         * Response code variable
         * @access public
         * @type bool Determines whether to include the response code, default: false
         * */
        public $get_code = false;

        /**
         * Cache ttl for all get requests
         * @access public
         * $type int TTL in seconds
         */
        public $cache_ttl = 1;
        public $message = '';

        /**
         * Cache folder
         * @access public
         * $type string Cache dir
         */
        public $cache_dir = '/tmp/vultr-api-client-cache';
        private $request_type;

        /**
         * Constructor function
         * @param string $token
         * @param int $cache_ttl
         * @return void
         * @see https://my.vultr.com/settings/
         */
        public function __construct(string $token, $cache_ttl = 1)
        {
            $this->api_token = $token;
            $this->cache_ttl = $cache_ttl;
            $this->account = self::account_info();
        }

        /**
         * Get Account info
         * @see https://www.vultr.com/api/#tag/account
         * @return mixed
         */
        public function account_info()
        {
            return self::get('account');
        }

        /**
         * Get OS list
         * @see https://www.vultr.com/api/#operation/list-os
         * @return mixed
         */
        public function os_list()
        {
            return self::get('os');
        }

        /**
         * OS Upgrade/Change Options
         * @see https://www.vultr.com/api/#operation/get-instance-upgrades
         * @see
         * @param mixed $subid
         * @return mixed
         */
        public function os_change_list($subid)
        {
            return self::get("instances/{$subid}/upgrades?type=os");
        }

        /**
         * OS Change/ Reinstall
         * @see https://www.vultr.com/api/#operation/update-instance
         * @see
         * @param mixed $subid
         * @param mixed $osid // https://www.vultr.com/api/#operation/list-isos
         * @return mixed
         */
        public function os_change($subid, $osid)
        {
            return self::patch("instances/{$subid}", array('os_id' => $osid));
        }

        /**
         * List available ISO iamges
         * @see https://www.vultr.com/api/#iso_list
         * @return mixed Available ISO images
         * */
        public function iso_list()
        {
            return self::get('iso');
        }

        /**
         * Attach ISO
         * @see https://www.vultr.com/api/#operation/attach-instance-iso
         * @param mixed $subid
         * @param mixed $isoid
         * @return mixed
         */
        public function attach_iso($subid, $isoid)
        {
            return self::post("instances/{$subid}/iso/attach", array('iso_id' => $isoid));
        }

        /**
         * Detach ISO
         * @see https://www.vultr.com/api/#operation/detach-instance-iso
         * @param mixed $subid
         * @return mixed
         */
        public function detach_iso($subid)
        {
            return self::post("instances/{$subid}/iso/detach");
        }

        /**
         * Get ISO Status
         * @see https://www.vultr.com/api/#operation/get-instance-iso-status
         * @param [type] $subid
         * @return mixed
         */
        public function iso_status($subid)
        {
            return self::get("instances/{$subid}/iso");
        }

        /**
         * List available snapshots
         * @see https://www.vultr.com/api/#snapshot_snapshot_list
         * @return mixed
         */
        public function snapshot_list()
        {
            return self::get('snapshots');
        }

        /**
         * Destroy snapshot
         * @see https://www.vultr.com/api/#snapshot_destroy
         * @param int $snapshot_id
         * @return int HTTP response code
         */
        public function snapshot_destroy(int $snapshot_id)
        {
            //$args = array('SNAPSHOTID' => $snapshot_id);
            return self::delete("snapshots/{$snapshot_id}");
        }

        /**
         * Create snapshot
         * @see https://www.vultr.com/api/#snapshot_create
         * @param int $server_id
         * @param $description
         * @return bool|int|mixed|string
         */
        public function snapshot_create(int $server_id, $description)
        {
            $args = array('instance_id' => $server_id, 'description' => $description);
            return self::post('snapshots', $args);
        }

        /**
         * Create Domain
         * @see https://www.vultr.com/api/#operation/create-dns-domain
         * @param string $domain
         * @param string $serverIP // optional IP for the Domain DNS
         */
        public function domain_create(string $domain, string $serverIP)
        {
            $args = array('domain' => $domain, 'ip' => $serverIP);
            return self::post('domains', $args);
        }

        /**
         * Delete Domain
         * @see https://www.vultr.com/api/#operation/delete-dns-domain-record
         * @param string $domain
         * @return bool|int|mixed|string
         */
        public function domain_delete(string $domain)
        {
            //$args = array('domain' => $domain);
            return self::delete("domains/{$domain}");
        }



        public function dns_list()
        {
            return self::get('domains');
        }

        public function dns_records($domain)
        {
            return self::get("domains/{$domain}/records");
        }

        public function create_record($domain, $args)
        {
            return self::post("domains/{$domain}/records", $args);
        }

        public function delete_record($domain, $recordids)
        {
            return self::delete("domains/{$domain}/records{$recordid}");
        }

        public function update_record($domain, $args)
        {
            return self::patch("domains/{$domain}/records{$recordid}", $args);
        }

        public function soa_update($domain, $args)
        {
            return self::patch("domains/{$domain}/soa", $args);
        }

        public function soa_info($domain, $args)
        {
            return self::get("domains/{$domain}/soa", $args);
        }

        public function upgrade_plan_list($subid)
        {
            return self::get("instances/{$subid}/upgrades" . '?type=plans');
        }

        /**
         * Upgrade Plan
         * @see https://www.vultr.com/api/#operation/update-instance
         * @param string $subid
         * @param integer $vpsplanid // https://www.vultr.com/api/#operation/list-plans
         * @return void
         */
        public function upgrade_plan(string $subid, int $vpsplanid)
        {
            return self::patch("instances/{$subid}", array('plan' => $vpsplanid));
        }

        /**
         * List available applications
         * @see https://www.vultr.com/api/#app_app_list
         * @return mixed Available applications
         * */
        public function app_list()
        {
            return self::get('applications');
        }

        /**
         * List available applications for instance
         * @see https://www.vultr.com/api/#operation/get-instance-upgrades
         * @return mixed Available applications
         */
        public function app_change_list($subid)
        {
            return self::get("instances/{$subid}/upgrades?type=applications");
        }

        /**
         * Reinstall via Application ID
         * @see https://www.vultr.com/api/#operation/update-instance
         * @param integer $subid // server
         * @param integer $appid // Available applications from https://www.vultr.com/api/#operation/list-applications
         * @return bool|int|mixed|string
         */
        public function app_change(int $subid, int $appid)
        {
            return self::patch("instances/{$subid}", array('app_id' => $appid));
        }

        /**
         * List available plans
         * @see https://www.vultr.com/api/#plans_plan_list
         * @return mixed
         */
        public function plans_list()
        {
            return self::get('plans');
        }

        /**
         * List available regions
         * @see https://www.vultr.com/api/#regions_region_list
         * @return mixed
         */
        public function regions_list()
        {
            return self::get('regions');
        }

        /**
         * Determine region availability
         * @see https://www.vultr.com/api/#regions_region_available
         * @param string $datacenter_id
         * @return mixed VPS plans available at given region
         */
        public function regions_availability(string $datacenter_id)
        {
            return self::get("regions/{$datacenter_id}/availability");
        }


        /**
         * Get Instance userdata
         * @param $server_id
         * @see https://www.vultr.com/api/#operation/get-instance-userdata
         * @return int|mixed|string
         */
        public function server_userData($server_id)
        {
            return self::get("instances/{$server_id}/user-data");
        }

        /**
         * List startup scripts
         * @see https://www.vultr.com/api/#startupscript_startupscript_list
         * @return mixed List of startup scripts
         */
        public function startupscript_list()
        {
            return self::get('startup-scripts');
        }

        /**
         * Update startup script
         * @see https://www.vultr.com/api/#operation/update-startup-script
         * @param int $script_id
         * @param string $name
         * @param string $script script contents
         * @param string $script_type
         * @return int HTTP response code
         */
        public function startupscript_update(int $script_id, string $name, string $script, $script_type='default')
        {
            $args = array(
                'type' => $script_type,
                'name' => $name,
                'script' => $script
            );
            return self::patch("startup-scripts/{$script_id}", $args);
        }

        /**
         * Destroy startup script
         * @see https://www.vultr.com/api/#startupscript_destroy
         * @param int $script_id
         * @return int HTTP respnose code
         */
        public function startupscript_destroy(int $script_id)
        {
            //$args = array('SCRIPTID' => $script_id);
            return self::delete("startup-scripts/{$script_id}");
        }

        /**
         * Create startup script
         * @see https://www.vultr.com/api/#operation/create-startup-script
         * @param string $script_name
         * @param string $script_contents
         * @return int Script ID
         */
        public function startupscript_create(string $script_name, string $script_contents, $script_type)
        {
            $args = array(
                'name' => $script_name,
                'script' => $script_contents,
                'type' => $script_type
            );
            $script = self::post('startup-scripts', $args);
            return $script['startup_script']['id'];
        }

        /**
         * @param $region_id
         * @param $plan_id
         * @throws Exception
         */
        public function server_available($region_id, $plan_id)
        {
            $availability = self::regions_availability($region_id);
            if (!in_array($plan_id, $availability)) {
                throw new Exception('Plan ID ' . $plan_id . ' is not available in region ' . $region_id);
            }
        }

        /**
         * List servers
         * @see https://www.vultr.com/api/#server_server_list
         * @return mixed List of servers
         */
        public function server_list()
        {
            return self::get('instances');
        }

        /**
         * Display server bandwidth
         * @see https://www.vultr.com/api/#operation/get-instance-bandwidth
         * @param int $server_id
         * @return mixed Bandwidth history
         */
        public function bandwidth(int $server_id)
        {
            //$args = array('SUBID' => (int)$server_id);
            return self::get("instances/{$server_id}/bandwidth");
        }

        /**
         * List IPv4 Addresses allocated to specified server
         * @see https://www.vultr.com/api/#operation/get-instance-ipv4
         * @param int $server_id
         * @return mixed IPv4 address list
         */
        public function list_ipv4(int $server_id)
        {
            //$args = array('SUBID' => (int)$server_id);
            $ipv4 = self::get("instances/{$server_id}/ipv4");
            return $ipv4[(int)$server_id];
        }

        /**
         * Create IPv4 address
         * @see https://www.vultr.com/api/#operation/create-instance-ipv4
         * @param int $server_id
         * @param string Reboot server after adding IP: <yes|no>, default: yes
         * @return int HTTP response code
         * */
        public function ipv4_create(int $server_id, $reboot = 'yes')
        {
            $args = array(
                'reboot' => ($reboot == 'yes' ? 'yes' : 'no')
            );
            return self::post("instances/{$server_id}/ipv4", $args);
        }

        /**
         * Destroy IPv4 Address
         * @see https://www.vultr.com/api/#operation/delete-instance-ipv4
         * @param $server_id
         * @param $ip4
         * @return int HTTP response code
         */
        public function destroy_ipv4($server_id, $ip4)
        {
            return self::delete("instances/{$server_id}/ipv4/{$ip4}");
        }

        /**
         * Set Reverse DNS for IPv4 address
         * @see https://www.vultr.com/api/#operation/create-instance-reverse-ipv4
         * @param string $ip
         * @param string $rdns
         * @param $id
         * @return int HTTP response code
         */
        public function reverse_set_ipv4(string $ip, string $rdns, $id)
        {
            $args = array(
                'ip' => $ip,
                'reverse' => $rdns

            );
            return self::post("instances/{$id}/ipv4/reverse", $args);
        }

        /**
         * Set Default Reverse DNS for IPv4 address
         * @see https://www.vultr.com/api/#operation/post-instances-instance-id-ipv4-reverse-default
         * @param string $server_id
         * @param string $ip
         * @return int HTTP response code
         */
        public function reverse_default_ipv4(string $server_id, string $ip)
        {
            $args = array(
                //'reverse' => (int)$server_id,
                'ip' => $ip
            );
            return self::post("instances/{$server_id}/reverse/default", $args);
        }

        /**
         * List IPv6 addresses for specified server
         * @see https://www.vultr.com/api/#operation/get-instance-ipv6
         * @param int $server_id
         * @return mixed IPv6 allocation info
         */
        public function list_ipv6(int $server_id)
        {
            $ipv6 = self::get("instances/{$server_id}/ipv6");
            return $ipv6[(int)$server_id];
        }

        /**
         * List Instance IPv6 Reverse
         * @see https://www.vultr.com/api/#operation/list-instance-ipv6-reverse
         * @param int $server_id
         * @return mixed IPv6 allocation info
         */
        public function reverse_list_ipv6(int $server_id)
        {
            $ipv6 = self::get("instances/{$server_id}/ipv6/reverse");
            return $ipv6[(int)$server_id];
        }

        /**
         * Get Application Information
         * @see https://www.vultr.com/api/#operation/get-instance
         * @param int $server_id
         * @return mixed Application Information
         */
        public function get_app_info(int $server_id)
        {
            //$args = array('SUBID' => (int)$server_id);
            $app_id = self::get("instances/{$server_id}")['app_id'];
            $app_info = self::get("instances/{$server_id}")['applications'][$app_id];
            return $app_info['name'];
        }


        /**
         * Set Reverse DNS for IPv6 address
         * @see https://www.vultr.com/api/#operation/create-instance-reverse-ipv6
         * @param int $server_id
         * @param string $ip
         * @param string $rdns
         * @return int HTTP response code
         */
        public function reverse_set_ipv6(int $server_id, string $ip, string $rdns)
        {
            $args = array(
                'ip' => $ip,
                'reverse' => $rdns
            );
            return self::post("instances/{$server_id}/ipv6/reverse", $args);
        }

        /**
         * Delete IPv6 Reverse DNS
         * @see https://www.vultr.com/api/#operation/delete-instance-reverse-ipv6
         * @param int $server_id
         * @param string $ip6 IPv6 address
         * @return int HTTP response code
         * */
        public function reverse_delete_ipv6(int $server_id, string $ip6)
        {
            return self::delete("instances/{$server_id}/ipv6/reverse/{$ip6}");
        }

        /**
         * Reboot server
         * @see https://www.vultr.com/api/#operation/reboot-instance
         * @param int $server_id
         * @return int HTTP response code
         */
        public function reboot(int $server_id)
        {
            return self::post("instances/{$server_id}/reboot");
        }

        /**
         * Halt server
         * @see https://www.vultr.com/api/#operation/halt-instances
         * @param int $server_id
         * @return int HTTP response code
         */
        public function halt(int $server_id)
        {
            $args = array('instance_ids' => array((int)$server_id));
            return self::post('instances/halt', $args);
        }

        /**
         * Start server
         * @see https://www.vultr.com/api/#operation/start-instance
         * @param int $server_id
         * @return int HTTP response code
         */
        public function start(int $server_id)
        {
            return self::post("instances/{$server_id}/start");
        }

        /**
         * Destroy server
         * @see https://www.vultr.com/api/#operation/delete-instance
         * @param int $server_id
         * @return int HTTP response code
         */
        public function destroy(int $server_id)
        {
            return self::delete("instances/{$server_id}");
        }

        /**
         * Reinstall OS on an instance
         * @see https://www.vultr.com/api/#operation/reinstall-instance
         * @param int $server_id
         * @param string $hostname
         * @return int HTTP response code
         */
        public function reinstall(int $server_id, $hostname='')
        {
            $args = array('hostname' => $hostname);
            return self::post("instances/{$server_id}/reinstall", $args);
        }

        /**
         * Set server label
         * @see https://www.vultr.com/api/#operation/update-instance
         * @param int $server_id
         * @param string $label
         * @return int HTTP response code
         */
        public function label_set(int $server_id, string $label)
        {
            $args = array(
                'label' => $label
            );
            return self::patch("instances/{$server_id}", $args);
        }

        /**
         * Restore Server Snapshot
         * @see https://www.vultr.com/api/#operation/restore-instance
         * @param int $server_id
         * @param string $snapshot_id Hexadecimal string with Restore ID
         * @return int HTTP response code
         */
        public function restore_snapshot(int $server_id, string $snapshot_id)
        {
            $args = array(
                'snapshot_id' => preg_replace('/[^a-f0-9]/', '', $snapshot_id)
            );
            return self::post("instances/{$server_id}/restore", $args);
        }

        /**
         * Restore Backup
         * @see https://www.vultr.com/api/#operation/restore-instance
         * @param int $server_id
         * @param string $backup_id
         * @return int HTTP response code
         */
        public function restore_backup(int $server_id, string $backup_id)
        {
            $args = array(
                'backup_id' => $backup_id
            );
            return self::post("instances/{$server_id}/restore", $args);
        }

        /**
         * List Backups
         * @see https://www.vultr.com/api/#operation/list-backups
         * @return mixed
         */
        public function backup_list()
        {
            return self::get('backups');
        }

        /**
         * Server Create
         * @see https://www.vultr.com/api/#operation/create-instance
         * @param $config
         * @return bool|int|mixed|string
         */
        public function create($config)
        {
            try {
                self::server_available((int)$config['DCID'], (int)$config['VPSPLANID']);
            } catch (Exception $e) {
                return false;
            }

            return self::post('instances', $config);
        }

        /**
         * SSH Keys List method
         * @see https://www.vultr.com/api/#sshkey_sshkey_list
         * @return FALSE if no SSH keys are available
         * @return mixed with whatever ssh keys get returned
         */
        public function sshkeys_list()
        {
            $try = self::get('ssh-keys');
            if (sizeof($try) < 1) {
                return false;
            }
            return $try;
        }

        /**
         * SSH Keys Create method
         * @see https://www.vultr.com/api/#sshkey_sshkey_create
         * @param string $name
         * @param string $key [openssh formatted public key]
         * @return FALSE if no SSH keys are available
         * @return mixed with whatever ssh keys get returned
         */
        public function sshkey_create(string $name, string $key)
        {
            $args = array(
                'name' => $name,
                'ssh_key' => $key
            );
            return self::post('ssh-keys', $args);
        }

        /**
         * SSH Keys Update method
         * @see https://www.vultr.com/api/#sshkey_sshkey_update
         * @param string $key_id
         * @param string $name
         * @param string $key [openssh formatted public key]
         * @return int HTTP response code
         */
        public function sshkey_update(string $key_id, string $name, string $key)
        {
            $args = array(
                'SSHKEYID' => $key_id,
                'name' => $name,
                'ssh_key' => $key
            );
            return self::patch('sshkey/update', $args);
        }

        /**
         * SSH Keys Destroy method
         * @see https://www.vultr.com/api/#sshkey_sshkey_destroy
         * @param string $key_id
         * @return int HTTP response code
         */
        public function sshkey_destroy(string $key_id)
        {
            $args = array('SSHKEYID' => $key_id);
            return self::delete('sshkey/destroy', $args);
        }

        /**
         * GET Method
         * @param string $method
         * @param mixed $args
         * @return int|mixed|string
         */
        public function get(string $method, $args = false)
        {
            $this->request_type = 'GET';
            $this->get_code = false;
            return self::query($method, $args);
        }


        /**
         * DELETE Method
         * @param $method
         * @param $args
         * @return bool|int|mixed|string
         */
        public function delete($method, $args=[])
        {
            $this->request_type = 'DELETE';
            return self::query($method, $args);
        }


        /**
         * PATCH Method
         * @param $method
         * @param $args
         * @return bool|int|mixed|string
         */
        public function patch($method, $args)
        {
            $this->request_type = 'PATCH';
            return self::query($method, $args);
        }

        /**
         * POST Method
         * @param $method
         * @param $args
         * @return bool|int|mixed|string
         */
        public function post($method, $args)
        {
            $this->request_type = 'POST';
            return self::query($method, $args);
        }

        /**
         * PUT Method
         * @param $method
         * @param $args
         * @return bool|int|mixed|string
         */
        public function put($method, $args)
        {
            $this->request_type = 'PUT';
            return self::query($method, $args);
        }

        /**
         * API Query Function
         * @param string $method
         * @param mixed $args
         * @return int|mixed|string
         */
        private function query(string $method, $args)
        {
            $methodArray = explode('/', $method);
            $apiRequiredArray = array(
                'account',
                'auth',
                'backup',
                'baremetal',
                'block',
                'dns',
                'firewall',
                'iso',
                'network',
                'plans',
                'reservedip',
                'server',
                'snapshot',
                'sshkey',
                'startupscript',
                'user',
                'type',
                'per_page',
                'instance_id',
                'tag',
                'label',
                'main_ip',
                'cursor'
            );

            $url = $this->endpoint . $method;
            $apiRequired = false;

            if ($this->debug) {
                echo $this->request_type . ' ' . $url . PHP_EOL;
            }

            $_defaults = array(
                CURLOPT_USERAGENT => sprintf('%s v%s (%s) - WHMCS Module', $this->agent, $this->version, 'https://github.com/whattheserver/vultr-provisioning-module/'),
                CURLOPT_HEADER => 0,
                CURLOPT_VERBOSE => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_HTTP_VERSION => '1.0',
                CURLOPT_FOLLOWLOCATION => 0,
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => array('Accept: application/json')
            );

            if (in_array($methodArray[0], $apiRequiredArray)) {
                array_push($_defaults[CURLOPT_HTTPHEADER], "Authorization: Bearer $this->api_token");
                $apiRequired = true;
            }

            $cacheable = false;
            switch ($this->request_type) {

                case 'POST':
                    $post_data = http_build_query($args);
                    $_defaults[CURLOPT_URL] = $url;
                    $_defaults[CURLOPT_POST] = 1;
                    $_defaults[CURLOPT_POSTFIELDS] = $post_data;
                    break;

                case 'PUT':
                    $post_data = http_build_query($args);
                    $_defaults[CURLOPT_URL] = $url;
                    $_defaults[CURLOPT_CUSTOMREQUEST] = 'PUT';
                    $_defaults[CURLOPT_POSTFIELDS] = $post_data;
                    break;

                case 'PATCH':
                    $post_data = http_build_query($args);
                    $_defaults[CURLOPT_URL] = $url;
                    $_defaults[CURLOPT_CUSTOMREQUEST] = 'PATCH';
                    $_defaults[CURLOPT_POSTFIELDS] = $post_data;
                    break;

                case 'DELETE':
                    //$post_data = http_build_query($args);
                    $_defaults[CURLOPT_URL] = $url;
                    $_defaults[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                    //$_defaults[CURLOPT_POSTFIELDS] = $post_data;
                    break;

                case 'GET':
                    if ($args !== false) {
                        $get_data = http_build_query($args);
                        $_defaults[CURLOPT_URL] = $url . '?' . $get_data;
                    } else {
                        $_defaults[CURLOPT_URL] = $url;
                    }

                    $cacheable = true;
                    $response = $this->serveFromCache($_defaults[CURLOPT_URL]);
                    if ($response !== false) {
                        $this->response_code = 200;
                        return $response;
                    }
                    break;

                default:
                    break;
            }

            // To avoid rate limit hits
            if ($this->readLast() == time() && $apiRequired) {
                usleep(333, 334);
            }

            $apisess = curl_init();
            curl_setopt_array($apisess, $_defaults);
            $response = curl_exec($apisess);
            $httpCode = curl_getinfo($apisess, CURLINFO_HTTP_CODE);
            logModuleCall('Vultr', $url, $args, "HTTP Code: " . $httpCode . "\n" . $response);
            $this->writeLast();

            /**
             * Check to see if there were any API exceptions thrown
             * If so, then error out, otherwise, keep going.
             */
            try {
                self::isAPIError($apisess, $response);
            } catch (Exception $e) {
                curl_close($apisess);
                $message = $e->getMessage() . PHP_EOL;
                $this->message = $message;
                return $message;
            }

            /**
             * Close our session
             * Return the decoded JSON response
             */
            curl_close($apisess);
            $obj = json_decode($response, true);

            if ($this->get_code) {
                return (int)$this->response_code;
            }

            if ($cacheable) {
                $this->saveToCache($url, $response);
            } else {
                $this->purgeCache($url);
            }

            return $obj;
        }

        public function getCode()
        {
            return (int)$this->response_code;
        }

        public function checkConnection(): bool
        {
            return $this->getCode() == 200;
        }

        public function getMessage(): string
        {
            return $this->message;
        }

        /**
         * API Error Handling
         * @param cURL_Handle $response_obj
         * @param string $response
         * @throws Exception if invalid API location is provided
         * @throws Exception if API token is missing from request
         * @throws Exception if API method does not exist
         * @throws Exception if Internal Server Error occurs
         * @throws Exception if the request fails otherwise
         */
        public function isAPIError(cURL_Handle $response_obj, string $response)
        {
            $code = curl_getinfo($response_obj, CURLINFO_HTTP_CODE);
            $this->response_code = $code;

            if ($this->debug) {
                echo $code . PHP_EOL;
            }

            switch ($code) {
                case 200:
                    break;
                case 201:
                    break;
                case 202:
                    break;
                case 204:
                    break;
                case 400:
                    throw new Exception('400: Bad Request');
                    break;
                case 401:
                    throw new Exception('401: Unauthorized');
                    break;
                case 403:
                    throw new Exception('403: Forbidden');
                    break;
                case 404:
                    throw new Exception('404: Not Found');
                    break;
                case 500:
                    throw new Exception('500: Internal Server Error');
                    break;
                case 503:
                    throw new Exception('503: Service Unavailable. Your request exceeded the API rate limit.');
                    break;
                case 412:
                    throw new Exception('Request failed: ' . $response);
                    break;
                default:
                    break;
            }
        }

        protected function serveFromCache($url)
        {
            // garbage collect 5% of the time
            if (mt_rand(0, 19) == 0) {
                $files = glob("$this->cache_dir/*");
                $old = time() - ($this->cache_ttl * 2);
                foreach ($files as $file) {
                    if (filemtime($file) < $old) {
                        unlink($old);
                    }
                }
            }

            $hash = md5($url);
            $group = $this->groupFromUrl($url);
            $file = "$this->cache_dir/$group-$hash";
            if (file_exists($file) && filemtime($file) > (time() - $this->cache_ttl)) {
                $response = file_get_contents($file);
                $obj = json_decode($response, true);
                return $obj;
            }
            return false;
        }

        protected function saveToCache($url, $json)
        {
            if (!file_exists($this->cache_dir)) {
                mkdir($this->cache_dir);
            }

            $hash = md5($url);
            $group = $this->groupFromUrl($url);
            $file = "$this->cache_dir/$group-$hash";
            file_put_contents($file, $json);
        }

        protected function groupFromUrl($url)
        {
            $group = 'default';
            if (preg_match('@/v1/([^/]+)/@', $url, $match)) {
                return $match[1];
            }
        }

        protected function purgeCache($url)
        {
            $group = $this->groupFromUrl($url);
            $files = glob("$this->cache_dir/$group-*");
            foreach ($files as $file) {
                unlink($file);
            }
        }

        protected function writeLast()
        {
            if (!file_exists($this->cache_dir)) {
                mkdir($this->cache_dir);
            }

            file_put_contents("$this->cache_dir/last", time());
        }

        protected function readLast()
        {
            if (file_exists("$this->cache_dir/last")) {
                return file_get_contents("$this->cache_dir/last");
            }
        }
    }
}
