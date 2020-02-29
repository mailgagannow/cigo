<?php
// print_R($_SERVER['DOCUMENT_ROOT']);EXIT;
use kartik\grid\GridView;

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\models\Order;
use kartik\editable\Editable;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Existing Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet"
	href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
	integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
	crossorigin="" />
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
	integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
	crossorigin=""></script>
<div class="order-index">

	<h1><?= Html::encode($this->title) ?></h1>

   

    <?php
    $gridColumns = [
        'first_name',
        'last_name',
        'order_date',
        [
            'attribute' => 'status',
            'label' => false,

            'class' => 'kartik\grid\EditableColumn',

            'editableOptions' => function ($model) {

                return [

                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => Order::getStatus(), // any list of values
                    'formOptions' => [
                        'action' => Url::to([
                            'order/editable',
                            'id' => $model->id
                        ])
                    ]
                ];
            },

            'value' => function ($data) {
                return Order::getStatus($data->status);
            }
        ],
        [
            'attribute' => 'order_type',
            'label' => false,
            'value' => function ($model) {
                return $model->getType($model->order_type);
            },
            'contentOptions' => [
                'style' => 'display:none'
            ]
        ],
        [
            'attribute' => 'id',
            'label' => false,
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->status === Order::STATUS_ASSIGNED || $model->status == Order::STATUS_PENDING) {
                    return "<i id='first-$model->id' class='fa fa-times-circle fa-2x' data-id='$model->id' aria-hidden='true' style='color:red '></i>"; // "x" icon in red color
                } else {
                    return "<i id='second' data-id='$model->id' class='fa fa-times-circle fa-2x' style='color:red'></i>"; // "x" icon in red color
                }
            }
        ],
        [
            'attribute' => 'latitude',
            'label' => false,
            'contentOptions' => [
                'style' => 'display:none'
            ],
            'headerOptions' => [
                'style' => 'display:none'
            ]
        ],
        [
            'attribute' => 'longitude',
            'label' => false,
            'contentOptions' => [
                'style' => 'display:none'
            ],
            'headerOptions' => [
                'style' => 'display:none'
            ]
        ],
        [
            'attribute' => 'id',
            'label' => false,
            'contentOptions' => [
                'style' => 'display:none'
            ],
            'headerOptions' => [
                'style' => 'display:none'
            ]
        ]
    ];
    ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    // 'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => [
        'style' => 'overflow: auto'
    ], // only set when $responsive = false

    'toolbar' => '',

    'pjax' => false,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'summary' => false,

    // 'floatHeader' => true,
    // 'floatHeaderOptions' => ['top' => $scrollingTop],
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ]
]);
?>





<script type="text/javascript">
$(document).ready(function () {
	$(document).find("i[id^='first-']").on('click', function(){
	    var num = this.id.split('-')[1];
	    var confirmText = "Are you sure you want to delete this item?";
	    if(confirm(confirmText)) {
		    var id=$(this).attr("data-id");

	    	 $.ajax({
	             url: '<?=Url::toRoute(['order/delete'])?>',
	             type: "POST",
	             data: {
	                 id:id
	             },
	             
	         });
	        
	    }
	    return false;
	});
	
	  var el = document.getElementsByClassName("kv-editable-value kv-editable-link");
	  for (var i = 0; i < el.length; i++){
		 
		  
		    el[i].classList.add("mystyle");
	  }
	  var style = document.getElementsByClassName("kv-editable-value kv-editable-link mystyle");
	  for (var i = 0; i < style.length; i++){
		  if(style[i].textContent=='Cancelled'){
			    style[i].style.backgroundColor = '#0048BA';
			    style[i].style.backgroundColor = '#FF4040';
			    
			    
		  }
		  if(style[i].textContent=='Assigned'){
			    style[i].style.backgroundColor = '#0048BA';
			    
			    
		  }
		  if(style[i].textContent=='Done'){
			    style[i].style.backgroundColor = '#5FED00';
			    
			    
		  }
		  if(style[i].textContent=='On Route'){
			    style[i].style.backgroundColor = '#FFDF00';
		  }
		  if(style[i].textContent=='Pending'){
			    style[i].style.color = '#000000';
			    style[i].style.backgroundColor = '#F5F5F5';
			    
		  }
				  
	  }
	  
	 // el[0].classList.add("mystyle");
	  var row= document.getElementsByTagName("tr");
	 
	    
	
	  var img= {<?=Order::STATUS_ASSIGNED?>:'005-calendar.png' ,<?=Order::STATUS_CANCELLED?>:'016-delivery-failed.png' ,<?=Order::STATUS_DONE?>: '015-delivered.png',<?=Order::STATUS_PENDING?>:'057-stopwatch.png',<?=Order::STATUS_ROUTE?>:'028-express-delivery.png'}
	  var mymap = "";
	mymap=  L.map('mapid').setView([47.751076, -120.740135], 13);
	  
	  
	  
	  L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
		    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		    maxZoom: 18,
		    id: 'mapbox/streets-v11',
		    tileSize: 512,
		    zoomOffset: -1,
		    accessToken: 'pk.eyJ1IjoibWFpbGdhZ2Fubm93IiwiYSI6ImNrNzVnenRuNTA1bngzam5zdTU2b3FkcjgifQ.DzWv8UjNDdwhDYkYAm77cQ'
		}).addTo(mymap);
		var latlng="";
		var tr=0;
		  var markers = {};
			var coordinates=[ ];
	  $('.w1').each(function() {
		  var latitude="";
		  var longitude="";
		  var icon="";
		  var orderId="";
		  var orderType="";
		  var orderDate="";	  
		  $(this).find('td').each(function() {
		    var cell = $(this).index(); 
		    if(cell==2){
		    	orderDate=$(this).html();
		    }
		    
		    if(cell==3){
		    	var e = document.getElementById("order-"+tr+"-status");
		    	var option = e.options[e.selectedIndex].value;
			    icon=img[option];
			    tr=tr+1;
		    }

		    if(cell==4){
		    	orderType=$(this).html();
			    
		    }
		    
			    if(cell==6){
				     latitude=$(this).html();
				    

			    }

			    if(cell==7){
				     longitude=$(this).html();
					    
			    }
			    if(cell==8){
				   // console.log($(this).html());
			  	  orderId= $(this).html();
			  	  
			  	 
				    
				  
					    
			    }
			 
			    if(latitude!=0 &&  longitude!=0 &&orderId!=null){
			    	var greenIcon = L.icon({
			    	    iconUrl: '/cigo-tracker/views/order/'+icon,

			    	    iconSize:     [25, 70], // size of the icon
			    	    shadowSize:   [50, 64], // size of the shadow
			    	    iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
			    	    shadowAnchor: [4, 62],  // the same for the shadow
			    	    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
			    	});
			    	
			    	 latlng = L.latLng({ lat: latitude, lng: longitude });
			    		markers[orderId] = L.marker(latlng,{icon:greenIcon}).addTo(mymap)
						.bindPopup("<b>The type of your order is "+orderType+" and schedule date is "+orderDate+"</b>");
				         
						coordinates.push(latlng);			    
			    
			   
		    }
	        

		  });
			
		});
	  var bounds = new L.LatLngBounds(coordinates);
	  mymap.fitBounds(bounds);
	  $("#preview").click(function() { 
		  var street="";
		  var city="";
		  var state="";
		  var country="";
		  var previewLat="";
		  var previewLong="";
		street=  document.getElementById("order-street_address").value;
		city=document.getElementById("order-city").value;
		state=document.getElementById("order-state").value;
		country=document.getElementById("order-country").value;
		
		
		  
			  if(city=="" || street=="" || state=="" || country==""){
				  alert('Please enter the full address');
				  return false;
			  }
			  $.ajax({
				    type: "POST",
				    url: "<?=Url::toRoute(['order/get-coordinates'])?>",
				    data: {
				        street: street,
				        city:city,
				        state:state,
				        country:country
				    },
				    success: function (data) {
				        console.log(data);
				        if(data.message==""&&(data.lng!="" && data.lat!="")){
				        	
					        previewLat=data.lat;
					        previewLong=data.lng;
							markers['preview']= L.marker([previewLat, previewLong]).addTo(mymap);
						
							    console.log(markers);
								
									mymap.setView([previewLat, previewLong], 13);
										
						    	 }else{
							alert("Sorry not able to preview the location");
							return false;
					        }

				    }
				});
			   
		    
			
	  });
	  $('tr').click(function() {
	      markers[$(this).find('td:eq(8)').text()].openPopup();
		});

	  $('#reset').click(function() {
		  mymap.removeLayer(markers['preview']);
		  
		  mymap.fitBounds(bounds);
	      
		});
		
	
	
	
	



	   
});
</script>