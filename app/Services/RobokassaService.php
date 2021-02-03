<?php


namespace App\Services;

use App\Models\Shop\PremiumSubscription;
use Illuminate\Support\Facades\Http;
use App\Models\Shop\SubscriptionOrders;

class RobokassaService
{
    private $login;
    private $pass;
    private $desc = 'Оплата премиум-доступа Radio-Nice';

    public function __construct($login, $pass) {
        $this->login = $login;
        $this->pass = $pass;
    }

    public function getRedirectUrl(PremiumSubscription $subscription, $user) {
        $userId = $user->id;
        $price = $subscription->price;
        $subscriptionId = $subscription->id;
        $invId = SubscriptionOrders::count() + 1;
        $signature = $this->getSignature($price, $invId, $subscriptionId, $userId);
        return "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin={$this->login}&OutSum={$price}&InvId={$invId}&Email={$user->email}&Shp_subscription=${subscriptionId}&Shp_user={$userId}&Description={$this->desc}&SignatureValue={$signature}";
    }

    public function getSignature($price, $invId, $subscriptionId, $userId) {
        return md5("{$this->login}:{$price}:{$invId}:{$this->pass}:Shp_subscription={$subscriptionId}:Shp_user={$userId}");
    }


}
