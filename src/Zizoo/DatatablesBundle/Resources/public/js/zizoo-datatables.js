$.fn.dataTableExt.oPagination.zizoo_pagination = {

    "fnInit": function ( oSettings, nPaging, fnCallbackDraw )
    {
        var nPaginationContainer = document.createElement('div'),
            nPrevious = document.createElement( 'a' ),
            nNext = document.createElement( 'a' ),
            nPages = document.createElement( 'ol' );

        nPrevious.appendChild( document.createElement('span') ); 
        nPrevious.appendChild( document.createTextNode( oSettings.oLanguage.oPaginate.sPrevious ) );
        nNext.appendChild( document.createTextNode( oSettings.oLanguage.oPaginate.sNext ) );
        nNext.appendChild( document.createElement('span') );

        nPaginationContainer.appendChild(nPrevious);
        nPaginationContainer.appendChild(nPages);
        nPaginationContainer.appendChild(nNext);

        nPaginationContainer.className = "pagination";
        nPrevious.className = "arrow prev";
        nPrevious.title = oSettings.oLanguage.oPaginate.sPrevious;
        nNext.className="arrow next";
        nNext.title = oSettings.oLanguage.oPaginate.sNext;

        nPaging.appendChild( nPaginationContainer );
         
        $(nPrevious).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "previous" );
            fnCallbackDraw( oSettings );
        } );
         
        $(nNext).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "next" );
            fnCallbackDraw( oSettings );
        } );
                          
        /* Disallow text selection */
        $(nPrevious).bind( 'selectstart', function () { return false; } );
        $(nNext).bind( 'selectstart', function () { return false; } );
    },
     
    /*
     * Function: oPagination.four_button.fnUpdate
     * Purpose:  Update the list of page buttons shows
     * Returns:  -
     * Inputs:   object:oSettings - dataTables settings object
     *           function:fnCallbackDraw - draw function which must be called on update
     */
    "fnUpdate": function ( oSettings, fnCallbackDraw )
    {
        var iListLength = 5;
        var an = oSettings.aanFeatures.p;
        var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

        var iTotalPages = Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength),
            iPage = Math.floor(oSettings._iDisplayStart / oSettings._iDisplayLength);

        if ( iTotalPages < iListLength) {
            iStart = 1;
            iEnd = iTotalPages;
        }
        else if ( iPage <= iHalf ) {
            iStart = 1;
            iEnd = iListLength;
        } else if ( iPage >= (iTotalPages-iHalf) ) {
            iStart = iTotalPages - iListLength + 1;
            iEnd = iTotalPages;
        } else {
            iStart = iPage - iHalf + 1;
            iEnd = iStart + iListLength - 1;
        }

        for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
            // Remove the middle elements
            $('li', an[i]).remove();

            // Add the new list items and their event handlers
            for ( j=iStart ; j<=iEnd ; j++ ) {
                sClass = (j==iPage+1) ? 'class="current"' : '';
                $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                    .appendTo( $('ol', an[i])[0] )
                    .bind('click', function (e) {
                        e.preventDefault();
                        oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oSettings._iDisplayLength;
                        fnCallbackDraw( oSettings );
                    });
            }
        }
    }
};

$.fn.dataTableExt.afnFiltering.push(
    function( oSettings, aData, iDataIndex ) {
        var iFini = $("#custom_filter input[name=start_date]").val();
        var iFfin = $("#custom_filter input[name=end_date]").val();
        
        var iDateCol = 3;
         
        iFini=iFini.substring(0,4) + iFini.substring(5,7)+ iFini.substring(8,10);
        iFfin=iFfin.substring(0,4) + iFfin.substring(5,7)+ iFfin.substring(8,10);      
         
        var datofini=aData[iDateCol].substring(0,4) + aData[iDateCol].substring(5,7)+ aData[iDateCol].substring(8,10);
         
        if ( iFini == "" && iFfin == "" )
        {
            return true;
        }
        else if ( iFini <= datofini && iFfin == "")
        {
            return true;
        }
        else if ( iFfin >= datofini && iFini == "")
        {
            return true;
        }
        else if (iFini <= datofini && iFfin >= datofini)
        {
            return true;
        }
        return false;
    }
);

$.fn.dataTableExt.oStdClasses.sSortable = "sort";
$.fn.dataTableExt.oStdClasses.sSortAsc = "sort ascending";
$.fn.dataTableExt.oStdClasses.sSortDesc = "sort descending";