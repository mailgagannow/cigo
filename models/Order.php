<?php
namespace app\models;

use Yii;
use Geocodio\Geocodio;

/**
 * This is the model class for table "{{%tbl_order}}".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property int $order_type
 * @property string|null $amount
 * @property string $order_date
 * @property string|null $street_address
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string|null $zipcode
 */
class Order extends \yii\db\ActiveRecord
{

    const STATUS_PENDING = 1;

    const STATUS_ASSIGNED = 2;

    const STATUS_ROUTE = 3;

    const STATUS_DONE = 4;

    const STATUS_CANCELLED = 5;

    const ORDER_DELIVERY = 1;

    const ORDER_SERVICING = 2;

    const ORDER_INSTALLING = 3;

    /**
     *
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tbl_order}}';
    }

    /**
     *
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'first_name',
                    'last_name',
                    'order_type',
                    'order_date',
                    'city',
                    'state',
                    'country',
                    'contact_no',
                    'street_address'
                ],
                'required'
            ],
            [
                [
                    'order_type',
                    'status'
                ],
                'integer'
            ],
            [
                [
                    'order_date'
                ],
                'safe'
            ],
            [
                [
                    'first_name',
                    'last_name',
                    'email',
                    'amount',
                    'street_address',
                    'city',
                    'state',
                    'country',
                    'zipcode',
                    'contact_no',
                    'latitude',
                    'longitude'
                ],
                'string',
                'max' => 124
            ]
        ];
    }

    /**
     *
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'email' => Yii::t('app', 'Email'),
            'contact_no' => Yii::t('app', 'Phone Number'),
            'order_type' => Yii::t('app', 'Order Type'),
            'status' => Yii::t('app', 'Order Status'),
            'amount' => Yii::t('app', 'Amount'),
            'order_date' => Yii::t('app', 'Order Date'),
            'street_address' => Yii::t('app', 'Street Address'),
            'city' => Yii::t('app', 'City'),
            'state' => Yii::t('app', 'State'),
            'country' => Yii::t('app', 'Country'),
            'zipcode' => Yii::t('app', 'Zipcode')
        ];
    }

    public static function getType($type_id = null)
    {
        $array = array(
            Order::ORDER_DELIVERY => "Delivery",
            Order::ORDER_INSTALLING => "Installing",
            Order::ORDER_SERVICING => "Servicing"
        );
        if (! empty($type_id)) {
            if (array_key_exists($type_id, $array)) {
                return $array[$type_id];
            }
        }

        return $array;
    }

    public static function getStatus($status = null)
    {
        $array = array(
            Order::STATUS_PENDING => "Pending",
            Order::STATUS_ASSIGNED => "Assigned",
            Order::STATUS_ROUTE => "On Route",
            Order::STATUS_DONE => "Done",
            Order::STATUS_CANCELLED => "Cancelled"
        );
        if (! empty($status)) {
            if (array_key_exists($status, $array)) {
                return $array[$status];
            }
        }
        return $array;
    }

    public function getCoordinates($model = null)
    {
        $message = "";
        $result = [];
        $geocoder = new Geocodio();
        $geocoder->setApiKey('8c755571b5e51658e1858e6759e9d9e58555e68');
        // $geocoder->setHostname('api-hipaa.geocod.io'); // optionally overwrite the API hostname
        try {
            $response = $geocoder->geocode([
                'street' => $model->street_address,
                'city' => $model->city,
                'state' => $model->state,
                'country' => $model->country
            ]);

            $result['lat'] = $response->results[0]->location->lat;
            $result['lng'] = $response->results[0]->location->lng;
        } Catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $result['message'] = $message;
        return $result;
    }
    
    public function getImages($status){
        $array = array(
            Order::STATUS_PENDING => "057-stopwatch.png",
            Order::STATUS_ASSIGNED => "005-calendar.png",
            Order::STATUS_ROUTE => "028-express-delivery.png",
            Order::STATUS_DONE => "015-delivered.png",
            Order::STATUS_CANCELLED => "016-delivery-failed.png"
        );
        if (! empty($status)) {
            if (array_key_exists($status, $array)) {
                return $array[$status];
            }
        }
        return $array;
    }
}
