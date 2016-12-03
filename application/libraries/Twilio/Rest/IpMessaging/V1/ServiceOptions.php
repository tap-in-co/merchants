<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\IpMessaging\V1;

use Twilio\Options;
use Twilio\Values;

abstract class ServiceOptions {
    /**
     * @param string $friendlyName The friendly_name
     * @param string $defaultServiceRoleSid The default_service_role_sid
     * @param string $defaultChannelRoleSid The default_channel_role_sid
     * @param string $defaultChannelCreatorRoleSid The
     *                                             default_channel_creator_role_sid
     * @param string $readStatusEnabled The read_status_enabled
     * @param string $typingIndicatorTimeout The typing_indicator_timeout
     * @param string $consumptionReportInterval The consumption_report_interval
     * @param string $webhooks The webhooks
     * @return UpdateServiceOptions Options builder
     */
    public static function update($friendlyName = Values::NONE, $defaultServiceRoleSid = Values::NONE, $defaultChannelRoleSid = Values::NONE, $defaultChannelCreatorRoleSid = Values::NONE, $readStatusEnabled = Values::NONE, $typingIndicatorTimeout = Values::NONE, $consumptionReportInterval = Values::NONE, $webhooks = Values::NONE) {
        return new UpdateServiceOptions($friendlyName, $defaultServiceRoleSid, $defaultChannelRoleSid, $defaultChannelCreatorRoleSid, $readStatusEnabled, $typingIndicatorTimeout, $consumptionReportInterval, $webhooks);
    }
}

class UpdateServiceOptions extends Options {
    /**
     * @param string $friendlyName The friendly_name
     * @param string $defaultServiceRoleSid The default_service_role_sid
     * @param string $defaultChannelRoleSid The default_channel_role_sid
     * @param string $defaultChannelCreatorRoleSid The
     *                                             default_channel_creator_role_sid
     * @param string $readStatusEnabled The read_status_enabled
     * @param string $typingIndicatorTimeout The typing_indicator_timeout
     * @param string $consumptionReportInterval The consumption_report_interval
     * @param string $webhooks The webhooks
     */
    public function __construct($friendlyName = Values::NONE, $defaultServiceRoleSid = Values::NONE, $defaultChannelRoleSid = Values::NONE, $defaultChannelCreatorRoleSid = Values::NONE, $readStatusEnabled = Values::NONE, $typingIndicatorTimeout = Values::NONE, $consumptionReportInterval = Values::NONE, $webhooks = Values::NONE) {
        $this->options['friendlyName'] = $friendlyName;
        $this->options['defaultServiceRoleSid'] = $defaultServiceRoleSid;
        $this->options['defaultChannelRoleSid'] = $defaultChannelRoleSid;
        $this->options['defaultChannelCreatorRoleSid'] = $defaultChannelCreatorRoleSid;
        $this->options['readStatusEnabled'] = $readStatusEnabled;
        $this->options['typingIndicatorTimeout'] = $typingIndicatorTimeout;
        $this->options['consumptionReportInterval'] = $consumptionReportInterval;
        $this->options['webhooks'] = $webhooks;
    }

    /**
     * The friendly_name
     * 
     * @param string $friendlyName The friendly_name
     * @return $this Fluent Builder
     */
    public function setFriendlyName($friendlyName) {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * The default_service_role_sid
     * 
     * @param string $defaultServiceRoleSid The default_service_role_sid
     * @return $this Fluent Builder
     */
    public function setDefaultServiceRoleSid($defaultServiceRoleSid) {
        $this->options['defaultServiceRoleSid'] = $defaultServiceRoleSid;
        return $this;
    }

    /**
     * The default_channel_role_sid
     * 
     * @param string $defaultChannelRoleSid The default_channel_role_sid
     * @return $this Fluent Builder
     */
    public function setDefaultChannelRoleSid($defaultChannelRoleSid) {
        $this->options['defaultChannelRoleSid'] = $defaultChannelRoleSid;
        return $this;
    }

    /**
     * The default_channel_creator_role_sid
     * 
     * @param string $defaultChannelCreatorRoleSid The
     *                                             default_channel_creator_role_sid
     * @return $this Fluent Builder
     */
    public function setDefaultChannelCreatorRoleSid($defaultChannelCreatorRoleSid) {
        $this->options['defaultChannelCreatorRoleSid'] = $defaultChannelCreatorRoleSid;
        return $this;
    }

    /**
     * The read_status_enabled
     * 
     * @param string $readStatusEnabled The read_status_enabled
     * @return $this Fluent Builder
     */
    public function setReadStatusEnabled($readStatusEnabled) {
        $this->options['readStatusEnabled'] = $readStatusEnabled;
        return $this;
    }

    /**
     * The typing_indicator_timeout
     * 
     * @param string $typingIndicatorTimeout The typing_indicator_timeout
     * @return $this Fluent Builder
     */
    public function setTypingIndicatorTimeout($typingIndicatorTimeout) {
        $this->options['typingIndicatorTimeout'] = $typingIndicatorTimeout;
        return $this;
    }

    /**
     * The consumption_report_interval
     * 
     * @param string $consumptionReportInterval The consumption_report_interval
     * @return $this Fluent Builder
     */
    public function setConsumptionReportInterval($consumptionReportInterval) {
        $this->options['consumptionReportInterval'] = $consumptionReportInterval;
        return $this;
    }

    /**
     * The webhooks
     * 
     * @param string $webhooks The webhooks
     * @return $this Fluent Builder
     */
    public function setWebhooks($webhooks) {
        $this->options['webhooks'] = $webhooks;
        return $this;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.IpMessaging.V1.UpdateServiceOptions ' . implode(' ', $options) . ']';
    }
}