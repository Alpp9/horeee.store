<?php

namespace BeycanPress\YAIA\PluginHero;

class LicenseVerifier
{
    private $verifierAPI;

    /**
     * @param string $siteUrl
     * @return void
     */
    public function __construct(string $siteUrl)
    {
        $this->verifierAPI = $siteUrl . '?rest_route=/licensor-api/verify';
    }

    /**
     * It checks the validity of the purchase licenseCode you entered and returns true false.
     * 
     * @param string $licenseCode
     * @param string|null $productcode
     * @return bool
     */
    public function verify(string $licenseCode, ?string $productcode = null) : ?object
    {

        $headers = ["Content-Type: application/json"];

        $curl = curl_init($this->verifierAPI);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_REFERER => $_SERVER["SERVER_NAME"],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode([
                "licenseCode" => trim($licenseCode),
                "productCode" => trim($productcode)
            ]),
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $resp = json_decode(curl_exec($curl));

        curl_close($curl);
        
        return $resp ? $resp : null;
    }
}