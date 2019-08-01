<?php

/**
 * Vultr.com Curl Adapter
 *
 * NOTE: part of this code was extracted from
 * https://github.com/usefulz/vultr-api-client, updated for PSR compliance and
 * extended with new API calls.
 *
 * @package Vultr
 * @version 1.0
 * @author  https://github.com/malc0mn - https://github.com/usefulz
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @see     https://github.com/malc0mn/vultr-api-client
 */

namespace Vultr\ApiCall;

use Vultr\Exception\ApiException;

class Server extends AbstractApiCall
{
    /**
     * Changes the application of a virtual machine.
     *
     * All data will be permanently lost.
     *
     * @see https://www.vultr.com/api/#server_app_change
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param integer $appId    Application to use. See getAppChangeList().
     *
     * @return integer HTTP response code
     */
    public function appChange($serverId, $appId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'APPID' => (int) $appId,
        ];

        return $this->adapter->post('server/app_change', $args, true);
    }

    /**
     * Retrieves a list of applications to which a virtual machine can be
     * changed.
     *
     * Only virtual machines originally launched as an application can be
     * switched to other applications. Always check against this list before
     * trying to switch applications because it is not possible to switch
     * between every application combination.
     *
     * @see https://www.vultr.com/api/#server_app_change_list
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getAppChangeList($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->get('server/app_change_list', $args);
    }

    /**
     * Changes the operating system of a virtual machine.
     *
     * All data will be permanently lost.
     *
     * @see https://www.vultr.com/api/#server_os_change
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param integer $osId     Operating system to use. See getOsChangeList().
     *
     * @return integer HTTP response code
     */
    public function osChange($serverId, $osId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'OSID' => (int) $osId,
        ];

        return $this->adapter->post('server/os_change', $args, true);
    }

    /**
     * Retrieves a list of operating systems to which a virtual machine can be
     * changed.
     *
     * @see https://www.vultr.com/api/#server_os_change_list
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getOsChangeList($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->get('server/os_change_list', $args);
    }


    /**
     * Upgrade the plan of a virtual machine.
     *
     * The virtual machine will be rebooted upon a successful upgrade.
     *
     * @see https://www.vultr.com/api/#server_upgrade_plan
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param integer $planId   The new plan. See getUpgradePlanList().
     *
     * @return integer HTTP response code
     */
    public function upgradePlan($serverId, $planId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'VPSPLANID' => (int) $planId,
        ];

        return $this->adapter->post('server/upgrade_plan', $args, true);
    }

    /**
     * Retrieve a list of the VPSPLANIDs for which a virtual machine can be
     * upgraded.
     *
     * An empty response array means that there are currently no upgrades
     * available.
     *
     * @see https://www.vultr.com/api/#server_upgrade_plan_list
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getUpgradePlanList($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->get('server/upgrade_plan_list', $args);
    }

    /**
     * List all active or pending virtual machines on the current account.
     *
     * The 'status' field represents the status of the subscription and will be
     * one of: pending | active | suspended | closed.
     * If the status is 'active', you can check 'power_status' to determine if
     * the VPS is powered on or not. When status is 'active', you may also use
     * 'server_state' for a more detailed status of: none | locked |
     * installingbooting | isomounting | ok.
     * The API does not provide any way to determine if the initial installation
     * has completed or not. The 'v6_network', 'v6_main_ip', and
     * 'v6_network_size' fields are deprecated in favor of 'v6_networks'.
     *
     * @see https://www.vultr.com/api/#server_server_list
     *
     * @param integer $subscriptionId (optional) Unique identifier of a
     * subscription. Only the subscription object will be returned.
     * @param string  $tag            (optional) A tag string. Only subscription
     * objects with this tag will be returned.
     *
     * @return array
     */
    public function getList($subscriptionId = null, $tag = null)
    {
        $args = [];

        if ($subscriptionId !== null) {
            $args['SUBID'] = $subscriptionId;
        }

        if ($tag !== null) {
            $args['tag'] = $tag;
        }

        $servers = $this->adapter->get('server/list', $args);

        return $servers;
    }

    /**
     * Set the tag of a virtual machine.
     *
     * Note tag is simply a string assign(ed) to a server. Default values in
     * web panel are "Cache", "DNS", ... . But you can use it in a key value way
     * like this "key:value" to make it more similar to other clouds(AWS, Azure, ...)
     *
     * @see https://www.vultr.com/api/#server_tag_set
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string  $tag      A tag string. The tag to assign to this server.
     *
     * @return integer HTTP response code
     */
    public function setTag($serverId, $tag) {
        $args = [
            'SUBID' => (int) $serverId,
            'tag'    => $tag,
        ];

        return $this->adapter->post('server/tag_set', $args, true);
    }

    /**
     * Wrapper function around getList() to get the details for one server.
     *
     * @param integer $serverId Unique identifier of a subscription. Only the
     * subscription object will be returned.
     *
     * @return array
     */
    public function getDetail($serverId) {
        return $this->getList($serverId);
    }


    /**
     * Wrapper function around getList() to get the details for one server.
     *
     * @param string $tag A tag string. Only subscription objects with this tag
     * will be returned.
     *
     * @return array
     */
    public function getByTag($tag) {
        return $this->getList(null, $tag);
    }

    /**
     * Retrieves the (base64 encoded) user-data for this subscription.
     *
     * @see https://www.vultr.com/api/#server_get_user_data
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getUserData($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        $userData = $this->adapter->get('server/get_user_data', $args);

        return base64_decode($userData['userdata']);
    }

    /**
     * Sets the cloud-init user-data for this subscription.
     *
     * Note that user-datais not supported on every operating system, and is
     * generally only provided on instance startup.
     *
     * @see https://www.vultr.com/api/#server_set_user_data
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string  $userData Cloud-init user-data
     *
     * @return integer HTTP response code
     */
    public function setUserData($serverId, $userData)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'userdata' => base64_encode($userData),
        ];

        return $this->adapter->post('server/set_user_data', $args, true);
    }

    /**
     * Determine what other subscriptions are hosted on the same physical host
     * as a given subscription.
     *
     * @see https://www.vultr.com/api/#server_neighbors
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getNeighbors($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->get('server/neighbors', $args);
    }

    /**
     * Get the bandwidth used by a virtual machine.
     *
     * @see https://www.vultr.com/api/#server_bandwidth
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getBandwidth($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->get('server/bandwidth', $args);
    }

    /**
     * List the IPv4 information of a virtual machine.
     *
     * IP information is only available for virtual machines in the "active"
     * state.
     *
     * @see https://www.vultr.com/api/#server_list_ipv4
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getIpv4List($serverId)
    {
        $args = ['SUBID' => (int) $serverId];
        $ip = $this->adapter->get('server/list_ipv4', $args);

        return $ip[(int) $serverId];
    }

    /**
     * Add a new IPv4 address to a server.
     *
     * You will start being billed for this immediately. The server will be
     * rebooted unless you specify otherwise. You must reboot the server before
     * the IPv4 address can be configured.
     *
     * @see https://www.vultr.com/api/#server_create_ipv4
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string  $reboot   (optional, default 'yes') 'yes' or 'no'. If yes,
     * the server is rebooted immediately.
     *
     * @return integer HTTP response code
     */
    public function createIpv4($serverId, $reboot = 'yes')
    {
        $args = [
            'SUBID' => (int) $serverId,
            'reboot' => ($reboot == 'yes' ? 'yes' : 'no'),
        ];

        return $this->adapter->post('server/create_ipv4', $args, true);
    }

    /**
     * Removes a secondary IPv4 address from a server.
     *
     * Your server will be hard-restarted. We suggest halting the machine
     * gracefully before removing IPs.
     *
     * @see https://www.vultr.com/api/#server_destroy_ipv4
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string  $ip       IPv4 address to remove.
     *
     * @return integer HTTP response code
     */
    public function destroyIpv4($serverId, $ip)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'ip' => $ip,
        ];

        return $this->adapter->post('server/destroy_ipv4', $args, true);
    }

    /**
     * Set a reverse DNS entry for an IPv4 address of a virtual machine.
     *
     * Upon success, DNS changes may take 6-12 hours to become active.
     *
     * @see https://www.vultr.com/api/#server_reverse_set_ipv4
     *
     * @param string $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string $ip       IPv4 address used in the reverse DNS update.
     * These can be found with the getIpv4List() call.
     * @param string $rdns     reverse DNS entry
     *
     * @return integer HTTP response code
     */
    public function setReverseIpv4($serverId, $ip, $rdns)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'ip' => $ip,
            'entry' => $rdns,
        ];

        return $this->adapter->post('server/reverse_set_ipv4', $args, true);
    }

    /**
     * Set a reverse DNS entry for an IPv4 address of a virtual machine to the
     * original setting.
     *
     * Upon success, DNS changes may take 6-12 hours to become active.
     *
     * @see https://www.vultr.com/api/#server_reverse_default_ipv4
     *
     * @param string $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string $ip       IPv4 address used in the reverse DNS update.
     * These can be found with the getIpv4List() call.
     *
     * @return integer HTTP response code
     */
    public function restoreDefaultReverseIpv4($serverId, $ip)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'ip' => $ip,
        ];

        return $this->adapter->post('server/reverse_default_ipv4', $args, true);
    }

    /**
     * List the IPv6 information of a virtual machine.
     *
     * IP information is only available for virtual machines in the "active"
     * state. If the virtual machine does not have IPv6 enabled, then an empty
     * array is returned.
     *
     * @see https://www.vultr.com/api/#server_list_ipv6
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array|false False when no IPv6 available
     */
    public function getIpv6List($serverId)
    {
        $args = ['SUBID' => (int) $serverId];
        $ip = $this->adapter->get('server/list_ipv6', $args);

        return !empty($ip) ? $ip[(int) $serverId] : false;
    }

    /**
     * List the IPv6 reverse DNS entries of a virtual machine.
     *
     * Reverse DNS entries are only available for virtual machines in the
     * "active" state. If the virtual machine does not have IPv6 enabled, then
     * an empty array is returned.
     *
     * @see https://www.vultr.com/api/#server_reverse_list_ipv6
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array|false False when no IPv6 available
     */
    public function getReverseIpv6List($serverId)
    {
        $args = ['SUBID' => (int) $serverId];
        $ip = $this->adapter->get('server/reverse_list_ipv6', $args);

        return !empty($ip) ? $ip[(int) $serverId] : false;
    }

    /**
     * Set a reverse DNS entry for an IPv6 address of a virtual machine.
     *
     * Upon success, DNS changes may take 6-12 hours to become active.
     *
     * @see https://www.vultr.com/api/#server_reverse_set_ipv6
     *
     * @param string $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string $ip       IPv6 address used in the reverse DNS update.
     * These can be found with the getIpv6List() call.
     * @param string $rdns     reverse DNS entry
     *
     * @return integer HTTP response code
     */
    public function setReverseIpv6($serverId, $ip, $rdns)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'ip' => $ip,
            'entry' => $rdns
        ];

        return $this->adapter->post('server/reverse_set_ipv6', $args, true);
    }

    /**
     * Remove a reverse DNS entry for an IPv6 address of a virtual machine.
     *
     * Upon success, DNS changes may take 6-12 hours to become active.
     *
     * @see https://www.vultr.com/api/#server_reverse_delete_ipv6
     *
     * @param string $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string $ip       IPv6 address used in the reverse DNS update.
     * These can be found with the getIpv6List() call.
     *
     * @return integer HTTP response code
     */
    public function deleteReverseIpv6($serverId, $ip)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'ip' => $ip,
        ];

        return $this->adapter->post('server/reverse_delete_ipv6', $args, true);
    }

    /**
     * Reboot a virtual machine.
     *
     * This is a hard reboot (basically, unplugging the machine).
     *
     * @see https://www.vultr.com/api/#server_reboot
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function reboot($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->post('server/reboot', $args, true);
    }

    /**
     * Halt a virtual machine.
     *
     * This is a hard power off (basically, unplugging the machine). The data on
     * the machine will not be modified, and you will still be billed for the
     * machine. To completely delete a machine, see destroy()
     *
     * @see https://www.vultr.com/api/#server_halt
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function halt($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->post('server/halt', $args, true);
    }

    /**
     * Start a virtual machine.
     *
     * If the machine is already running, it will be restarted.
     *
     * @see https://www.vultr.com/api/#server_start
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function start($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->post('server/start', $args, true);
    }

    /**
     * Destroy (delete) a virtual machine.
     *
     * All data will be permanently lost, and the IP address will be released.
     * There is no going back from this call.
     *
     * @see https://www.vultr.com/api/#server_destroy
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function destroy($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->post('server/destroy', $args, true);
    }

    /**
     * Reinstall the operating system on a virtual machine.
     *
     * All data will be permanently lost, but the IP address will remain the
     * same. There is no going back from this call.
     *
     * @see https://www.vultr.com/api/#server_reinstall
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function reinstall($serverId)
    {
        $args = ['SUBID' => (int) $serverId];

        return $this->adapter->post('server/reinstall', $args, true);
    }

    /**
     * Set the label of a virtual machine.
     *
     * @see https://www.vultr.com/api/#server_label_set
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string $label     This is a text label that will be shown in the
     * control panel.
     *
     * @return integer HTTP response code
     */
    public function setLabel($serverId, $label)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'label' => $label
        ];

        return $this->adapter->post('server/label_set', $args, true);
    }

    /**
     * Restore the specified snapshot to the virtual machine.
     *
     * Any data already on the virtual machine will be lost.
     *
     * @see https://www.vultr.com/api/#server_restore_snapshot
     *
     * @param integer $serverId   Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string  $snapshotId (see snapshot()->getList()) to restore to this
     * instance
     *
     * @return integer HTTP response code
     */
    public function restoreSnapshot($serverId, $snapshotId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'SNAPSHOTID' => $snapshotId,
        ];

        return $this->adapter->post('server/restore_snapshot', $args, true);
    }

    /**
     * Restore the specified backup to the virtual machine.
     *
     * Any data already on the virtual machine will be lost.
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string  $backupId (see metaData()->getBackupList()) to restore to
     * this instance
     *
     * @return integer HTTP response code
     */
    public function restoreBackup($serverId, $backupId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'BACKUPID' => $backupId
        ];

        return $this->adapter->post('server/restore_backup', $args, true);
    }

    /**
     * Create a new virtual machine.
     *
     * You will start being billed for this immediately. The response only
     * contains the SUBID for the new machine. You should use v1/server/list to
     * poll and wait for the machine to be created (as this does not happen
     * instantly).
     *
     * @see https://www.vultr.com/api/#server_create
     *
     * @param array $config with the following keys:
     *     DCID integer Location to create this virtual machine in.
     *          See region()->getList()
     *     VPSPLANID integer Plan to use when creating this virtual machine. See
     *          metaData()->getPlansList()
     *     OSID integer Operating system to use. See metaData()->getOsList()
     *     ipxe_chain_url string (optional) If you've selected the 'custom'
     *                    operating system, this can be set to chainload the
     *                    specified URL on bootup, via iPXE
     *     ISOID string (optional)  If you've selected the 'custom' operating
     *           system, this is the ID of a specific ISO to mount during the
     *           deployment
     *     SCRIPTID integer (optional) If you've not selected a 'custom'
     *              operating system, this can be the SCRIPTID of a startup
     *              script to execute on boot. See startupscript()->getList()
     *     SNAPSHOTID string (optional) If you've selected the 'snapshot'
     *                operating system, this should be the SNAPSHOTID (see
     *                snapshot->getList()) to restore for the initial
     *                installation
     *     enable_ipv6 string (optional) 'yes' or 'no'.  If yes, an IPv6 subnet
     *                 will be assigned to the machine (where available)
     *     enable_private_network string (optional) 'yes' or 'no'. If yes,
     *                            private networking support will be added to
     *                            the new server.
     *     label string (optional) This is a text label that will be shown in
     *           the control panel
     *     SSHKEYID string (optional) List of SSH keys to apply to this server
     *              on install (only valid for Linux/FreeBSD).  See
     *              sshKey()->getList().  Separate keys with commas
     *     auto_backups string (optional) 'yes' or 'no'.  If yes, automatic
     *                  backups will be enabled for this server (these have an
     *                  extra charge associated with them)
     *     APPID integer (optional) If launching an application (OSID 186), this
     *           is the APPID to launch. See metaData()->getAppList().
     *     userdata string (optional) Base64 encoded cloud-init user-data
     *     notify_activate string (optional, default 'yes') 'yes' or 'no'. If
     *                     yes, an activation email will be sent when the server
     *                     is ready.
     *     ddos_protection (optional, default 'no') 'yes' or 'no'.  If yes, DDOS
     *                     protection will be enabled on the subscription (there
     *                     is an additional charge for this)
     *     reserved_ip_v4 string (optional) IP address of the floating IP to use
     *                    as the main IP of this server
     *     hostname string (optional) The hostname to assign to this server.
     *     tag string (optional) The tag to assign to this server.
     *     FIREWALLGROUPID string (optional) The firewall group to assign to
     *                            this server. See firewall()->getGroupList().
     *
     * @return mixed int|boolean Server ID if creation is successful, false
     * otherwise
     */
    public function create(array $config)
    {
        $regionId = (int) $config['DCID'];
        $planId   = (int) $config['VPSPLANID'];

        if (isset($config['userdata'])) {
            // Assume no base64 encoding has been applied when decoding fails!
            if (base64_decode($config['userdata'], true) === FALSE) {
                $config['userdata'] = base64_encode($config['userdata']);
            }
        }

        $this->isAvailable($regionId, $planId);

        $server = $this->adapter->post('server/create', $config);

        return (int) $server['SUBID'];
    }

    /**
     * Determine server availability for a given region and plan.
     *
     * @param integer $regionId Datacenter ID
     * @param integer $planId   VPS Plan ID
     *
     * @return bool
     *
     * @throws ApiException if VPS Plan ID is not available in specified region
     */
    public function isAvailable($regionId, $planId)
    {
        $region = new Region($this->adapter);

        $availability = $region->getAvailability((int) $regionId);
        if (!in_array((int) $planId, $availability)) {
            throw new ApiException(
                sprintf('Plan ID %d is not available in region %d.', $planId, $regionId)
            );
        } else {
            return true;
        }
    }

    /**
     * Set, change, or remove the firewall group currently applied to a server
     *
     * @see https://www.vultr.com/api/#server_firewall_group_set
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param string $firewallGroupId              The firewall group to apply
     *               to this server. A value of "0" means "no firewall group".
     *               See firewall->getGroupList().
     *
     * @return integer HTTP response code
     */
    public function setFirewallGroup($serverId, $firewallGroupId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'FIREWALLGROUPID' => $firewallGroupId
        ];

        return $this->adapter->post('server/firewall_group_set', $args, true);
    }

    /**
     * Retrieve the current ISO state for a given subscription
     *
     * The  returned  state  may be one  of: ready | isomounting | isomounted.
     * ISOID  will  only  be set when the mounted ISO exists in your library (
     * see iso()->getList ). Otherwise, it will read "0".
     *
     * @see https://www.vultr.com/api/#server_iso_status
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getIsoStatus($serverId)
    {
        $args = [
            'SUBID' => (int) $serverId,
        ];

        return $this->adapter->get('server/iso_status', $args);
    }

    /**
     * Detach the currently mounted ISO and reboot the server.
     *
     * @see https://www.vultr.com/api/#server_iso_detach
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function setIsoDetach($serverId)
    {
        $args = [
            'SUBID' => (int) $serverId,
        ];

        return $this->adapter->post('server/iso_detach', $args, true);
    }

    /**
     * Attach an ISO and reboot the server.
     *
     * @see https://www.vultr.com/api/#server_iso_attach
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param integer $isoId             The ISO that will be mounted. See the
     * iso()->getList() call.
     *
     * @return integer HTTP response code
     */
    public function setIsoAttach($serverId, $isoId)
    {
        $args = [
            'SUBID' => (int) $serverId,
            'ISOID' => (int) $isoId,
        ];

        return $this->adapter->post('server/iso_attach', $args, true);
    }

    /**
     * Retrieves the backup schedule for a server. All time values are in UTC.
     *
     * @see https://www.vultr.com/api/#server_backup_get_schedule
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return array
     */
    public function getBackupSchedule($serverId)
    {
        $args = [
            'SUBID' => (int) $serverId,
        ];

        return $this->adapter->post('server/backup_get_schedule', $args, false);
    }

    /**
     * Sets the backup schedule for a server. All time values are in UTC.
     *
     * @see https://www.vultr.com/api/#server_backup_get_schedule
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     * @param array $config with the following keys:
     *     cron_type string Backup cron type. Can be one of 'daily', 'weekly',
     *                      or 'monthly'.
     *     hour integer (optional) Hour value (0-23). Applicable to crons:
     *                             'daily', 'weekly', 'monthly'.
     *     dow integer (optional) Day-of-week value (0-6). Applicable to
     *                            crons: 'weekly'.
     *     dom integer (optional) Day-of-month value (1-28). Applicable to
     *                            crons: 'monthly'.
     *
     * @return integer HTTP response code
     */
    public function setBackupSchedule($serverId, array $config)
    {
        // First array is indeed $config so any SUBID present will be
        // overwritten with the $serverId parameter.
        $config = array_merge($config, ['SUBID' => $serverId]);

        return $this->adapter->post('server/backup_set_schedule', $config, true);
    }

    /**
     * Enables automatic backups on a server.
     *
     * @see https://www.vultr.com/api/#server_backup_enable
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function enableBackup($serverId)
    {
        $args = [
            'SUBID' => (int) $serverId,
        ];

        return $this->adapter->post('server/backup_enable', $args, true);
    }

    /**
     * Disables automatic backups on a server.
     *
     * @see https://www.vultr.com/api/#server_backup_disable
     *
     * @param integer $serverId Unique identifier for this subscription. These
     * can be found using the getList() call.
     *
     * @return integer HTTP response code
     */
    public function disableBackup($serverId)
    {
        $args = [
            'SUBID' => (int) $serverId,
        ];

        return $this->adapter->post('server/backup_disable', $args, true);
    }
}
