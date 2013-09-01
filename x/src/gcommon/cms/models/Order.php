<?php
/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 */
class Order extends CmsActiveRecord
{

    const ORDER_STATUS_CREATED = 0;
    const ORDER_STATUS_CONFIRM = 1;
    const ORDER_STATUS_SHIPING = 3;
    const ORDER_STATUS_PAY = 2;
    const ORDER_STATUS_COMOLETE = 4;
    const ORDER_STATUS_CLOSED = 5;
    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('uid',$this->uid,true);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('total_price',$this->total_price,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if (!empty($attributes['brand_id']) && $attributes['brand_id'] != $this->brand_id) {
            $attrs[] = 'brand_id';
            $this->brand_id = $attributes['brand_id'];
        }
        if (!empty($attributes['rank']) && $attributes['rank'] != $this->rank) {
            $attrs[] = 'rank';
            $this->rank = $attributes['rank'];
        }
        if (!empty($attributes['status']) && $attributes['status'] != $this->status) {
            $attrs[] = 'status';
            $this->status = $attributes['status'];
        }
        if (!empty($attributes['batch_number']) && $attributes['batch_number'] != $this->batch_number) {
            $attrs[] = 'batch_number';
            $this->batch_number = $attributes['batch_number'];
        }
        if (!empty($attributes['quantity']) && $attributes['quantity'] != $this->quantity) {
            $attrs[] = 'quantity';
            $this->quantity = $attributes['quantity'];
        }
        if (!empty($attributes['total_price']) && $attributes['total_price'] != $this->total_price) {
            $attrs[] = 'total_price';
            $this->total_price = $attributes['total_price'];
        }
        if (!empty($attributes['shop_price']) && $attributes['shop_price'] != $this->shop_price) {
            $attrs[] = 'shop_price';
            $this->shop_price = $attributes['shop_price'];
        }
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    /**
     * 
     * get order all datas count
     */
    public function getCounts($type=false,$dtype){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        if($type){
            $condition = $this->getCondition($type,$dtype);
            $criteria->addCondition($condition);
        }
        if($dtype==2){
            $criteria->addCondition("charge_status=1");
        }
        return  self::model()->count($criteria);
         
    }
        /**
     * 
     * get table order all datas condition
     */
    public function getCondition($type){
        if($type==1){
            return "to_days(created)=to_days(now())";
        }elseif ($type==2){
            return "to_days(now())-to_days(created)=1";
        }elseif ($type==3){
            return "WEEKOFYEAR(created)=WEEKOFYEAR(NOW())";
        }elseif ($type==4) {
            return "WEEKOFYEAR(created)=WEEKOFYEAR(DATE_SUB(now(),INTERVAL 1 week))";
        }elseif ($type==5) {
            return "MONTH(created)=MONTH(NOW()) and year(created)=year(now())";
        }elseif ($type==6) {
            return "MONTH(created)=MONTH(DATE_SUB(NOW(),interval 1 month))
and year(created)=year(now())";
        }elseif ($type==7) {
            return "QUARTER(created)=QUARTER(now())";
        }elseif ($type==8) {
            return "QUARTER(created)=QUARTER(DATE_SUB(now(),interval 1 QUARTER))";
        }elseif ($type==9) {
            return "YEAR(created)=YEAR(NOW())";
        }elseif ($type==10) {
            return "year(created)=year(date_sub(now(),interval 1 year))";
        }
    }
    /**
     * 
     * get table order all datas
     */
    public function getAllproducts($count,$page,$type=false,$dtype){
        
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        if($type){
            $condition = $this->getCondition($type);
            $criteria->addCondition($condition);
        }
        if($dtype==2){
            $criteria->addCondition("charge_status=1");
        }
        $criteria->order = "t.id DESC";
        $criteria->limit = $count;
        $criteria->offset = ($page - 1) * $count;
        $datas =self::model()->findAll($criteria);
        return $datas; 
    }
    /**
     * 
     * get order all datas count
     */
    public function doSearch($payid){
        $criteria = new CDbCriteria;
        $criteria->alias = "t";
        $criteria->compare('t.id',$payid);
        return  self::model()->findAll($criteria);
    }
}