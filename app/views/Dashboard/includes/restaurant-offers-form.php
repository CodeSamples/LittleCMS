<form id="offerForm" method="post" class="none">
    <div class="last">
        <h1><?php echo OFFER_TITLE; ?></h1>
    </div>
    <div>
        <label><?php echo OFFER; ?></label>
        <br />
        <textarea id="offer" name="offer" value="" /></textarea>
    </div>
    <div>
        <label><?php echo OFFER_PLUS; ?></label>
        <br />
        <textarea id="offer_plus" name="offer_plus" value="" /></textarea>
    </div>
    <div>
        <label><?php echo OFFER_DATED; ?></label>
        <br />
        <br class="clear" />
        <div id="datedOfferContainer"></div>
        <br class="clear" />
        <div id="addDatedOffer"><?php echo ADD; ?></div>
        <br class="clear" />
    </div>
    <div class="centerText last">
        <input type="hidden" class="restaurantId" name="restaurant_id" value="" />
        <input type="hidden" id="offer_dated" name="offer_dated" />
        <input type="submit" name="offer" value="<?php echo SAVE; ?>" />
    </div>
</form>
<div id="datedOfferTemplate" class="datedOffer none">
    <div>
        <label><?php echo OFFER_DATED_START; ?></label>
    </div>
    <div>
        <input type="text" id="offer_special_in_%s" class="dateSelector in" readonly="readonly" /> 
        <div class="datepicker in"><?php echo OFFER_DATED_DATEPICKER; ?></div> 
    </div>
    <div>
        <label><?php echo OFFER_DATED_END; ?></label> 
    </div>
    <div>
        <input type="text" id="offer_special_out_%s" class="dateSelector out" readonly="readonly" /> 
        <div class="datepicker out"><?php echo OFFER_DATED_DATEPICKER; ?></div> 
    </div>
    <div>
        <label><?php echo OFFER_DATED_TXT; ?></label> 
    </div>
    <textarea id="offer_special_text_%s" maxlength="130"></textarea> 
    <input type="hidden" class="id" value="offer_special_%s" />
    <div class="removeOfferButton"><?php echo DELETE; ?></div> 
</div>
<div id="offerFormBlocker" class="pageBlocker"></div>
<div id="offerFormPreloader" class="preloader"></div>
<div id="formOfferResult" class="ui-corner-all none">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b></b></p>
</div>
<link href="/css/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
<script type="text/javascript">

    var offerItemCounter = 0;
    var usedDates = [];

    function datepickerBeforeShowDay(date) {
        var dbVal = date.getFullYear() + '-' + pad(date.getMonth()+1, 2) + '-' + pad(date.getDate(), 2);
        if(usedDates.indexOf(dbVal) == -1)
            return [true,''];
        else
            return [false,''];
    }

    function datepickerOnSelect(dateText, inst) {
        var currentVal = $('#offer_dated').val();
        var selectedVal = inst.selectedYear + '-' + pad(inst.selectedMonth+1, 2) + '-' + pad(inst.selectedDay, 2);
        var oldValDate = new Date(inst.lastVal.split('/').reverse().join('-'));
        var oldVal = (oldValDate != 'Invalid Date') ? oldValDate.getFullYear() + '-' + pad(oldValDate.getMonth()+1, 2) + '-' + pad(oldValDate.getDate(), 2) : 'NaN';
        var containerId = $('#' + inst.id).parent().siblings('input.id').val();
        var parent = $('#' + inst.id).parent().parent();

        console.log(containerId);
        console.log(parent);
        
        if(currentVal.length > 0) {
            var newValArray = currentVal.split(',');
        } else {
            var newValArray = [];
        }

        if(!/NaN/.test(oldVal)) {
            usedDates.splice(usedDates.indexOf(oldVal), 1);
        }

        var arrayPos = -1;
        for(var i = 0; i < newValArray.length; i++) {
            var itemRegExp = new RegExp(containerId);
            if(itemRegExp.test(newValArray[i])) {
                arrayPos = i;
                break;
            }
        }

        var date = '';
        if($('#' + inst.id).hasClass('in')) {
            date = getDbDate($(parent).find('.dateSelector.in').datepicker('getDate'));
        } else {
            date = '_' + getDbDate($(parent).find('.dateSelector.out').datepicker('getDate'));
        }

        if(arrayPos == -1) {
            usedDates.push(selectedVal);
            newValArray.push(containerId + ':' + date);
        } else {
            var actualValArray = newValArray[arrayPos].split(':');
            usedDates.push(date.replace(/_/, ''));
            var itemVal = actualValArray[1].split('_');
            if($('#' + inst.id).hasClass('in')) {
                itemVal[0] = date;
            } else {
                itemVal[1] = date.replace(/_/, '');
            }
            newValArray[arrayPos] = actualValArray[0] + ':' + itemVal.join('_');
        }
        $('#offer_dated').val(newValArray.join(','));
    }

    function getDbDate(date) {
        if(date != null) {
            return date.getFullYear() + '-' + pad(date.getMonth()+1, 2) + '-' + pad(date.getDate(), 2);
        } else {
            return '';
        }
    }

    function pad(num, size) {
        var a = num + '';
        while (a.length < size) a = '0' + a;
        return a;
    }

    function updateOfferText(event) {
        var offerId = $(event.currentTarget).siblings('input.id').val();
        var actualVal = $('#offer_dated').val();
        var arrayPos = -1;
        var offerIdRegExp = new RegExp(offerId);
        var saveText = $(event.currentTarget).val().replace(/,/g, '&#44;');
        saveText = saveText.replace(/_/g, ' ');
        saveText = saveText.replace(/\r|\n/g, ' ');

        if(actualVal.length > 0) {
            var newValArray = actualVal.split(',');
        } else {
            var newValArray = [];
        }
        for(var i = 0; i < newValArray.length; i++) {
            if(offerIdRegExp.test(newValArray[i])) {
                arrayPos = i;
                break;
            }
        }
        if(arrayPos > -1) {
            var actualValArray = newValArray[arrayPos].split(':');
            var theValArray = actualValArray[1].split('_');
            theValArray[2] = saveText;
            newValArray[arrayPos] = offerId + ':' + theValArray.join('_');
        } else {
            newValArray.push(offerId + ':' + '__' + saveText);
        }
        $('#offer_dated').val(newValArray.join(','));
    }

    function removeOffer(event) {
        var offerId = $(event.currentTarget).siblings('input.id').val();
        var currentOfferVal = $('#offer_dated').val();
        if(currentOfferVal.length > 0) {
            var offerRegExp = new RegExp(offerId);
            var valArray = currentOfferVal.split(',');
            var arrayPos = -1;

            for(var i = 0; i < valArray.length; i++) {
                if(offerRegExp.test(valArray[i])) {
                    arrayPos = i;
                    break;
                }
            }
            if(arrayPos > -1) {
                var values = valArray[arrayPos].split(':');
                var valuesArray = values[1].split('_');
                for (var i = 0; i < valuesArray.length; i++) {
                    if(/\d{4}-\d{2}-\d{2}/.test(valuesArray[i])) {
                        usedDates.splice(usedDates.indexOf(valuesArray[i]), 1);
                    }
                }
                valArray.splice(arrayPos, 1);
                $('#offer_dated').val(valArray.join(','));
            }
        }
        $(event.currentTarget).parent().remove();
    }

    function addOffer(event) {
        var item = $('#datedOfferTemplate').clone();
        $(item).attr('id', 'datedOffer_' + offerItemCounter);
        $(item).find('.dateSelector.in').attr('id', 
            $(item).find('.dateSelector.in').attr('id').replace(/%s/g, offerItemCounter));
        $(item).find('.dateSelector.out').attr('id', 
            $(item).find('.dateSelector.out').attr('id').replace(/%s/g, offerItemCounter));
        $(item).find('.id').val($(item).find('.id').val().replace(/%s/g, offerItemCounter));
        $(item).find('textarea').attr('id', 
            $(item).find('textarea').attr('id').replace(/%s/g, offerItemCounter));
        $(item).find('.dateSelector').datepicker({
            dateFormat:'dd/mm/yy',
            regional: 'es',
            beforeShowDay: datepickerBeforeShowDay,
            onSelect: datepickerOnSelect
        });
        $(item).find('textarea').on('keyup', updateOfferText);
        $(item).find('.datepicker').button({
            icons: {
                primary: 'ui-icon-calendar'
            }
        }).on('click', function(e){ 
            $(e.currentTarget).prev('.dateSelector').datepicker('show'); 
        });
        $(item).find('.removeOfferButton').button({
            icons: {
                primary: 'ui-icon-circle-close'
            }
        }).on('click', removeOffer);

        if(typeof event.settedValues != 'undefined') {
            $(item).find('.dateSelector.in').datepicker('setDate', event.settedValues.offer_in);
            $(item).find('.dateSelector.out').datepicker('setDate', event.settedValues.offer_out);
            $(item).find('textarea').val(event.settedValues.offer_text);
            usedDates.push(event.settedValues.offer_in);
            usedDates.push(event.settedValues.offer_out);
        }

        $(item).removeClass('none').appendTo('#datedOfferContainer');
        offerItemCounter++;        
    }


    function populateOfferForm(data) {
        $('#offer').val(data.offer);
        $('#offer_plus').jqteVal($('<div></div>').html(data.offer_plus).text());

        var inputValArray = [];
        for(var prop in data.offer_dated) {
            if(typeof data.offer_dated[prop] == 'object' && data.offer_dated[prop].length == 3) {
                inputValArray.push(prop + ':' + data.offer_dated[prop].join('_'));
                var startDateArr = data.offer_dated[prop][0].split('-');
                var startDate = new Date(startDateArr[0], startDateArr[1] - 1, startDateArr[2]);
                var endDateArr = data.offer_dated[prop][1].split('-');
                var endDate = new Date(endDateArr[0], endDateArr[1] - 1, endDateArr[2]);
                var fakeEvent = {};
                fakeEvent.currentTarget = $('#addOffer');
                fakeEvent.settedValues = {};
                fakeEvent.settedValues.offer_id = prop;
                fakeEvent.settedValues.offer_in = startDate;
                fakeEvent.settedValues.offer_out = endDate;
                fakeEvent.settedValues.offer_text = data.offer_dated[prop][2];
                addOffer(fakeEvent);
            }
        }
        $('#offer_dated').val(inputValArray.join(','));        
        $('#offerFormBlocker, #offerFormPreloader').hide();
    }
    

    function offerFormSubmit(event) {
        $.ajax({
            url: '/json/Offer/save',
            type: 'post',
            data: $(this).serialize(),
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                var className = 'ui-state-highlight';
                if(!response.response) {
                    className = 'ui-state-error';
                }
                $('#formOfferResult').removeClass('ui-state-highlight ui-state-error');
                $('#formOfferResult').addClass(className);
                $('#formOfferResult p b').html(response.messages[0]);
                $('#formOfferResult').dialog({
                    modal: true,
                    buttons: [
                        {
                            text: "<?php echo OK; ?>",
                            click: function() {
                                $(this).dialog('destroy');
                            }
                        }
                    ],
                    dialogClass: "no-close"
                });
            }
        })
        event.preventDefault();
        return false;
    }

    $(document).ready(function() {
        $('#addDatedOffer').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        });
        $('#addDatedOffer').bind('click', addOffer);
        $('#offer_plus').jqte();
        $('#offerForm').submit(offerFormSubmit);
    });

</script>