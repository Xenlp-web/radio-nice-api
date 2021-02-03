<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\SubscriptionOrders;
use Illuminate\Http\Request;
use App\Models\Shop\PremiumSubscription;
use Illuminate\Support\Facades\Validator;
use Robokassa;
use Illuminate\Support\Carbon;
use App\Events\PremiumPurchased;

class PremiumSubscriptionController extends Controller
{
    /**
     * Create new subscription
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request) {
        $validator = Validator::make($request->all(),
        [
            'period_in_months' => 'required|integer|unique:App\Models\Shop\PremiumSubscription',
            'price' => 'required|integer'
        ],
        [
            'period_in_months.required' => 'Не указано количество месяцев',
            'period_in_months.integer' => 'Количество месяцев не является целым числом',
            'period_in_months.unique' => 'Такой период уже существует',
            'price.required' => 'Не указана цена',
            'price.integer' => 'Цена не является целым числом'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            PremiumSubscription::create($request->all());
            return response()->json(['message' => 'Новая подписка успешно создана', 'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }


    /**
     * Edit subscription
     * @param Request $request
     * @param $subscriptionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $subscriptionId) {
        $validator = Validator::make($request->all(),
        [
            'period_in_months' => 'integer|unique:App\Models\Shop\PremiumSubscription,period_in_months,' . $subscriptionId,
            'price' => 'integer'
        ],
        [
            'period_in_months.integer' => 'Количество месяцев не является целым числом',
            'period_in_months.unique' => 'Такой период уже существует',
            'price.integer' => 'Цена не является целым числом'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            $subscription = PremiumSubscription::findOrFail($subscriptionId);
            $subscription->update($request->all());
            $subscription->save();
            return response()->json(['message' => 'Подписка успешно обновлена', 'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }


    /**
     * Deletes subscription
     * @param $subscriptionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($subscriptionId) {
        try {
            $subscription = PremiumSubscription::findOrFail($subscriptionId);
            $subscription->delete();
            return response()->json(['message' => 'Подписка успешно удалена', 'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }


    /**
     * Gets all subscriptions
     * @return \Illuminate\Http\JsonResponse
     */
    public function get() {
        return response()->json(['subscriptions' => PremiumSubscription::all(), 'status' => 'success']);
    }


    /**
     * Purchasing premium subscription
     * @param Request $request
     * @param int $subscriptionId
     * @return mixed
     */
    public function purchase(Request $request, int $subscriptionId) {
        $user = $request->user();

        try {
            $subscription = PremiumSubscription::findOrFail($subscriptionId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }

        return response()->json(['redirect_url' => Robokassa::getRedirectUrl($subscription, $user), 'status' => 'success']);
    }


    public function handleSuccessPurchase(Request $request) {
        $validator = Validator::make($request->all(), [
            'OutSum' => 'required',
            'InvId' => 'required',
            'SignatureValue' => 'required',
            'Shp_user' => 'required',
            'Shp_subscription' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            $subscription = PremiumSubscription::findOrFail($request->get('Shp_subscription'));
            $user = User::findOrFail($request->get('Shp_user'));

            PremiumPurchased::dispatch($user, $subscription->period_in_months);

            SubscriptionOrders::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'price' => $request->get('OutSum')
            ]);

            return 'OK' . $request->get('InvId');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }

    }
}
