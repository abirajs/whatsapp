<?php
/**
 * This file is used for retrieving, and updating settings data in the
 * Novalnet custom Settings table
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace NovalnetWhatsapp\Services;

use NovalnetWhatsapp\Models\Settings;
use Carbon\Carbon;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Modules\Plugin\PluginSet\Contracts\PluginSetRepositoryContract;
use Plenty\Plugin\Application;
use Plenty\Plugin\Log\Loggable;

/**
 * Class SettingsService
 *
 * @package Novalnet\Services\SettingsService
 */
class SettingsService
{
    use Loggable;

    /**
     * @var DataBase
     */
    protected $database;

    /**
     * Constructor.
     *
     * @param DataBase $database
     */
    public function __construct(DataBase $database)
    {
        $this->database = $database;
    }

    /**
     * Get Novalnet configuration
     *
     * @param  int $clientId
     * @param  int $pluginSetId
     *
     * @return array
     */
    public function getSettings($clientId = null, $pluginSetId = null)
    {
        if(is_null($clientId)) {
            /** @var Application $application */
            $application = pluginApp(Application::class);
            $clientId = $application->getPlentyId();
        }
        if(is_null($pluginSetId)) {
            /** @var PluginSetRepositoryContract $pluginSetRepositoryContract */
            $pluginSetRepositoryContract = pluginApp(PluginSetRepositoryContract::class);
            $pluginSetId = $pluginSetRepositoryContract->getCurrentPluginSetId();
        }
        /** @var Settings[] $setting */
        $settings = $this->database->query(Settings::class)->where('clientId', '=', $clientId)
                                  ->where('pluginSetId', '=', $pluginSetId)
                                  ->get();
        return $settings[0];
    }
	
    /**
     * Create or Update Novalnet Configurations values
     *
     * @param array $data
     * @param int $clientId
     * @param int $pluginSetId
     *
     * @return array
     */
    public function updateSettings($data, $clientId, $pluginSetId)
    {
        $novalnetSettings = $this->getSettings($clientId, $pluginSetId);
        if(!$novalnetSettings instanceof Settings) {
            /** @var Settings $settings */
            $novalnetSettings = pluginApp(Settings::class);
            $novalnetSettings->clientId = $clientId;
            $novalnetSettings->pluginSetId = $pluginSetId;
            $novalnetSettings->createdAt = (string)Carbon::now();
        }
        $novalnetSettings = $novalnetSettings->update($data);
        return $novalnetSettings;
    }

    /**
     * Get the individual configurations values
     *
     * @param string $settingsKey
     * @param string $paymentKey
     * @param int $clientId
     * @param int $pluginSetId
     *
     * @return mixed
     
    public function getPaymentSettingsValue($settingsKey = null, $paymentKey = null, $clientId = null, $pluginSetId = null)
    {
        
        $settings = $this->getSettings($clientId, $pluginSetId);
        $this->getLogger(__METHOD__)->error('settings', $settings);
	return 1;
    }
*/
    public function getPaymentSettingsValue($settingsKey, $paymentKey = null, $clientId = null, $pluginSetId = null)
    {
        
        $settings = $this->getSettings($clientId, $pluginSetId);
         $this->getLogger(__METHOD__)->error('SettingsReturn', $settings); 
        if(!is_null($settings)) {
            if(!empty($paymentKey) && isset($settings->value[$paymentKey])) {
                $this->getLogger(__METHOD__)->error('SettingsPaymentKey', $settings->value[$paymentKey]); 
                return $settings->value[$paymentKey][$settingsKey];
            } else {
                $this->getLogger(__METHOD__)->error('PaymentKey', $settings->value[$settingsKey]); 
                return $settings->value[$settingsKey];   
            }
        }
            /** $this->getLogger(__METHOD__)->error('Settings', $paymentKey); */
            return null;
    }

}
