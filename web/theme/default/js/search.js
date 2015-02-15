var asInitVals = new Array(); //for textinput filtering
var search_timeout = undefined;

//This is the search delay on the main filter textbox
jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function ( oSettings, iDelay ) {
    var
        _that = this,
        iDelay = (typeof iDelay == 'undefined') ? 400 : iDelay;
    
    this.each( function ( i ) {
        $.fn.dataTableExt.iApiIndex = i;
        var
            $this = this, 
            oTimerId = null, 
            sPreviousSearch = null,
            anControl = $( 'input', _that.fnSettings().aanFeatures.f );
        
            anControl.unbind( 'keyup' ).bind( 'keyup', function() {
            var $$this = $this;

            if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
                window.clearTimeout(oTimerId);
                sPreviousSearch = anControl.val();  
                oTimerId = window.setTimeout(function() {
                    $.fn.dataTableExt.iApiIndex = i;
                    _that.fnFilter( anControl.val() );
                }, iDelay);
            }
        });
        
        return this;
    } );
    return this;
}

/*
 jquery ready function for textwiki sortable tables
 mReschke
*/
$(document).ready(function() {
    var oTable = $('.datatable').dataTable({
        //Save State in Cookies
        "bStateSave": false,
        
        //This restores the footer column input filters on page reload (enable bStateSave)
        //http://datatables.net/forums/comments.php?DiscussionID=2864#Item_3
        //"fnInitComplete": function() {
        //    var oSettings = this.fnSettings();
        //    for ( var i=0; i < oSettings.aoPreSearchCols.length; i++ ) {
        //        if (oSettings.aoPreSearchCols[i].sSearch.length > 0) {
        //            $("tfoot input")[i].value = oSettings.aoPreSearchCols[i].sSearch;
        //            $("tfoot input")[i].className = "";
        //        }
        //    }
        //},
		
		//Turn off initial sorting, I let my model do the default sort
		"aaSorting": [],
		
		//Turn off main filter
		"bFilter": true,

        //Paging Style
        "bPaginate": true,
        "sPaginationType": "full_numbers",
        
        //Default Page Size
        "iDisplayLength": 25,

        //Set Language and Options for:
        "oLanguage": {
            //Text of all search textbox
            "sSearch": "",
            
            //Text and options of page length dropdown
            "sLengthMenu": '<select>'+
                '<option value="10">10</option>'+
                '<option value="15">15</option>'+
                '<option value="20">20</option>'+
                '<option value="25">25</option>'+
                '<option value="30">30</option>'+
                '<option value="40">40</option>'+
                '<option value="50">50</option>'+
                '<option value="75">75</option>'+
                '<option value="100">100</option>'+
                '<option value="-1">All</option>'+
                '</select>'
        }
	}).fnSetFilteringDelay();


    //N O T I C E
    //Column filtering does work, open Text_Wiki Render Table you will see where I commented it out
    //Just uncomment the <tboot><th>stuff and it will work.  I turned it off because it doesn't work when
    //there are multiple tables on the page

 
    //The Footer Column Filter Textboxes    
    //$("tfoot input").keyup( function () {
        //oTable.fnFilter( this.value, $("tfoot input").index(this) );
    //} );
    //This does solve typing one column, then tabbing to another
    //But I don't like how it refreshes page when you focusout
    //Solution for filter delay on individual column filters was http://datatables.net/forums/comments.php?DiscussionID=943 post by Dave177 Jan 24th 2011
    $("tfoot input").keyup( function (event) {
        if(event.keyCode!='9') {
            if(search_timeout != undefined) {
                clearTimeout(search_timeout);
            }
            $this = this;
            search_timeout = setTimeout(function() {
                search_timeout = undefined;
                oTable.fnFilter( $this.value, $("tfoot input").index($this) );
            }, 400);
        }
    } );
    //$("tfoot input").focusout( function () {
        //if(search_timeout != undefined) {
            //clearTimeout(search_timeout);
        //}
        //$this = this;
        //oTable.fnFilter( $this.value, $("tfoot input").index($this) );
    //} );

    $("tfoot input").each( function (i) {
        asInitVals[i] = this.value;
    } );
    $("tfoot input").focus( function () {
        if ( this.className == "search_init" ) {
            this.className = "";
            this.value = "";
        }
    } );
    $("tfoot input").blur( function (i) {
        if ( this.value == "" ) {
                this.className = "search_init";
                this.value = asInitVals[$("tfoot input").index(this)];
        }
    } );
    
    //Enable Fixed Headers
    //new FixedHeader( oTable );
});
