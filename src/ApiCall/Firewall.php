<?php

/**
 * Vultr.com PHP API Client
 *
 * @package Vultr
 * @version 1.0
 * @author  https://github.com/malc0mn
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @see     https://github.com/malc0mn/vultr-api-client
 */

namespace Vultr\ApiCall;

use Vultr\Exception\ApiException;

class Firewall extends AbstractApiCall
{
    /**
     * Create a new firewall group on the current account.
     *
     * @see https://www.vultr.com/api/#firewall_group_create
     *
     * @param string $description Description of firewall group.
     *
     * @return string The newly created firewall group id.
     */
    public function createGroup($description)
    {
        $args = [
            'description' => $description,
        ];

        $group = $this->adapter->post('firewall/group_create', $args);

        return $group['FIREWALLGROUPID'];
    }

    /**
     * Delete a firewall group.
     *
     * Use this function with caution because the firewall group being deleted
     * will be detached from all servers. This can result in open ports
     * accessible to the internet.
     *
     * @see https://www.vultr.com/api/#firewall_group_delete
     *
     * @param string $groupId Firewall group to delete.
     *
     * @return integer HTTP response code
     */
    public function deleteGroup($groupId)
    {
        $args = ['FIREWALLGROUPID' => $groupId];

        return $this->adapter->post('firewall/group_delete', $args, true);
    }

    /**
     * List all firewall groups on the current account.
     *
     * @see https://www.vultr.com/api/#dns_delete_domain
     *
     * @param string $groupId (optional) Filter result set to only contain this
     * firewall group.
     *
     * @return array
     */
    public function getGroupList($groupId = null)
    {
        $args = [];

        if ($groupId !== null) {
            $args['FIREWALLGROUPID'] = $groupId;
        }

        return $this->adapter->get('firewall/group_list', $args);
    }

    /**
     * Change the description on a firewall group.
     *
     * @see https://www.vultr.com/api/#firewall_group_set_description
     *
     * @param string  $groupId     Firewall group to update.
     * @param string  $description Description of firewall group.
     *
     * @return integer HTTP response code
     */
    public function setGroupDescription($groupId, $description)
    {
        $args = [
            'FIREWALLGROUPID' => $groupId,
            'description' => $description,
        ];

        return $this->adapter->post('firewall/group_set_description', $args, true);
    }

    /**
     * Create a rule in a firewall group.
     *
     * @see https://www.vultr.com/api/#firewall_rule_create
     *
     * @param string  $groupId    Target firewall group. @see getGroupList()
     * @param string  $ipType     IP address type. Possible values: "v4", "v6".
     * @param string  $protocol   Protocol type. Possible values: "icmp", "tcp",
     * "udp", "gre".
     * @param string  $subnet     IP address representing a subnet. The IP
     * address format must match with the "ip_type" parameter value.
     * @param integer $subnetSize IP prefix size in bits.
     * @param integer|string $port       (optional) TCP/UDP only. This field can be an
     * integer value specifying a port or a colon separated port range.
     * @param string  $direction  Direction of rule. Possible values: "in".
     *
     * @return integer The newly created rule number.
     */
    public function createRule(
        $groupId,
        $ipType,
        $protocol,
        $subnet,
        $subnetSize,
        $port = null,
        $direction = 'in'
    ) {
        $allowed = [
            'ipType' => ['v4', 'v6'],
            'protocol' => ['icmp', 'tcp', 'udp', 'gre'],
            'direction' => ['in'],
        ];

        // How can we turn this code block into a reusable function?
        foreach ($allowed as $param => $values) {
            // The double dollar sign here is NO mistake!
            if (!in_array($$param, $values)) {
                throw new ApiException(
                    sprintf(
                        '%s must be one of %s.',
                        ucfirst(strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $param))),
                        implode(' or ', $values)
                    )
                );
            }
        }

        $args = [
            'FIREWALLGROUPID' => $groupId,
            'direction' => $direction,
            'ip_type' => $ipType,
            'protocol' => $protocol,
            'subnet' => $subnet,
            'subnet_size' => (int) $subnetSize,
        ];

        if ($port !== null) {
            $args['port'] = $port;
        }

        $rule = $this->adapter->post('firewall/rule_create', $args);

        return (int) $rule['rulenumber'];
    }

    /**
     * Delete a rule in a firewall group.
     *
     * @see https://www.vultr.com/api/#firewall_rule_delete
     *
     * @param string  $groupId Target firewall group. See getGroupList().
     * @param integer $rulenumber Rule number to delete. See getRuleList().
     *
     * @return integer HTTP response code
     */
    public function deleteRule($groupId, $ruleNumber)
    {
        $args = [
            'FIREWALLGROUPID' => $groupId,
            'rulenumber' => $ruleNumber,
        ];

        return $this->adapter->post('firewall/rule_delete', $args, true);
    }

    /**
     * List the rules in a firewall group.
     *
     * @see https://www.vultr.com/api/#firewall_rule_list
     *
     * @param string $groupId   Target firewall group. See getGroupList()
     * @param string $ipType    IP address type. Possible values: "v4", "v6".
     * @param string $direction Direction of firewall rules. Possible values: "in".
     *
     * @return array
     */
    public function getRuleList($groupId, $ipType, $direction = 'in')
    {
        $allowed = [
            'ipType' => ['v4', 'v6'],
            'direction' => ['in'],
        ];

        // How can we turn this code block into a reusable function?
        foreach ($allowed as $param => $values) {
            // The double dollar sign here is NO mistake!
            if (!in_array($$param, $values)) {
                throw new ApiException(
                    sprintf(
                        '%s must be one of %s.',
                        ucfirst(strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $param))),
                        implode(' or ', $values)
                    )
                );
            }
        }

        $args = [
            'FIREWALLGROUPID' => $groupId,
            'direction' => $direction,
            'ip_type' => $ipType,
        ];

        return $this->adapter->get('firewall/rule_list', $args);
    }
}
