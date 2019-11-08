
<!--    http://amsul.github.io/pickadate.js -->
    <link rel="stylesheet" href="css/datePicker/default.css">
    <link rel="stylesheet" href="css/datePicker/default.date.css">

<!--         <form>
            <fieldset> -->
                

              
                <input 
                    style="float: left;"
                    id="datepicker"
                    class="datepicker"
                    name="date"
                    type="text"
                    autofocuss
                    value= "<?php echo date('j F, Y', strtotime($date)) ?>"
                    >

                <!-- <?php echo "The time is " . date("h:i:sa"); ?> -->
                

              
<!--             </fieldset>
        </form> -->

      
        <div id="container"></div>








  <script src= "js/jquery.1.7.0.js"></script>
   <script src = "js/datePicker/picker.js"></script>
   <script src = "js/datePicker/picker.date.js"></script>
   <script src = "js/datePicker/legacy.js"></script>

    <script type="text/javascript">

        var $input = $( '.datepicker' ).pickadate({
            // format: 'mm dd yyyy',
            formatSubmit: 'yyyy/mm/dd',
            // min: [2015, 7, 14],
            container: '#container',
            // editable: true,
            closeOnSelect: false,
            closeOnClear: false,
            disable: [true]
        })
        //test data
        // for(var i=0;i<validDates.length;i++){

        //   console.log(validDates[i])
        // }



        var picker = $input.pickadate('picker')

        var lastDatePicked=new Date(picker.get().select);



        function setValidDates(){
          picker = $input.pickadate('picker')
          picker.on({
          open: function() {
            console.log('Opened up!')
          },
          close: function() {
            console.log('Closed now')
            
          },
          render: function() {
            console.log('Just rendered anew')
            
          },
          stop: function() {
            console.log('See ya')
          },
          set: function(thingSet) {
            console.log('Just set')


            

            getInfoForNewDate(thingSet.select);

            if(thingSet.select){//acctually clicked on a date
              //console.log(dateSelected.getMonth());
              picker.close();
            }
            // console.log(thingSet.select)
            
 
            // $dateviewing = thingSet/1000; //add one day
            // $date= date('Y-m-d', strtotime($date));
            // document.getElementById("datelable").innerHTML=$date;

            //
          }
        })

          
           for(var i=0;i<validDates.length;i++){
              var day=new Date(validDates[i].replace(/-/g, '\/'));
              picker.set({
                disable:[day]
              })
            
          }
        }
        function isValidDate(){
          var validDates=picker.get('disable');
          var d=new Date(picker.get());
          for(var i=0;i<validDates.length;i++){
            var checkDate=new Date(validDates[i])
            if(d.getYear()==checkDate.getYear() &&
              d.getMonth()==checkDate.getMonth() &&
              d.getDate()==checkDate.getDate()){
              return true;
            }
          }
          return false;
        }


        

    </script>
