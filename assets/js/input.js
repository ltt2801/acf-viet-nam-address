(function($){
	var loading_billing = false;

	function initialize_field( $el ) {

		//$el.doStuff();

        if ( $el.parent('.row-clone').length === 0 && $el.parents('.clones').length === 0 ) {
            $( 'select.acf_viet_nam_select2', $el ).each( function() {
                var thisPlaceholder = $(this).data('placeholder');
                $(this).select2({
                    width : '100%',
                    placeholder: thisPlaceholder,
                });
            });
        }

        $( 'select.acf_viet_nam_select2.acf_vietnam_city' ).on( 'select2:select', function( object ) {
            update_city_address( this, object.params.data.id );
        });

        $( 'select.acf_viet_nam_select2.acf_vietnam_district' ).on( 'select2:select', function( object ) {
            acf_vietnam_district( this, object.params.data.id );
        });

	}

    function update_city_address( element, selected ) {
        var thisParent = $(element).parents('.acf-input'),
            matp = selected;
        var district = thisParent.find('select.acf_vietnam_district');
        var village = thisParent.find('select.acf_vietnam_village');
        var enable_new_admin_units = $(element).data('enable-new-admin-units') || 0;

        if(matp == ''){
            district.html('').select2({
                width : '100%',
                placeholder: district.data('placeholder'),
            });
            village.html('').select2({
                width : '100%',
                placeholder: village.data('placeholder'),
            });
        }else if(matp != '' && !loading_billing){
            loading_billing = true;
            $.ajax({
                type : "post",
                dataType : "json",
                url : devvn_acf_vn.admin_ajax,
                data : {action: "load_diagioihanhchinh", matp : matp, nonce : devvn_acf_vn.none_acfvn, enable_new_admin_units: enable_new_admin_units},
                context: this,
                success: function(response) {
                    if(enable_new_admin_units) {
                        // Nếu bật đơn vị hành chính mới, cập nhật village thay vì district
                        village.html('').select2({
                            width : '100%',
                            placeholder: village.data('placeholder'),
                        });
                        if(response.success) {
                            var listVillage = response.data;
                            var newState = new Option('', '');
                            village.append(newState);
                            $.each(listVillage,function(index,value){
                                var newState = new Option(value.name, value.ward_id);
                                village.append(newState);
                            });
                            loading_billing = false;
                        }
                    } else {
                        // Logic cũ cho đơn vị hành chính cũ
                        district.html('').select2({
                            width : '100%',
                            placeholder: district.data('placeholder'),
                        });
                        village.html('').select2({
                            width : '100%',
                            placeholder: village.data('placeholder'),
                        });
                        if(response.success) {
                            var listQH = response.data;
                            var newState = new Option('', '');
                            district.append(newState);
                            $.each(listQH,function(index,value){
                                var newState = new Option(value.name, value.maqh);
                                district.append(newState);
                            });
                            loading_billing = false;
                        }
                    }
                }
            });
        }
    }

    function acf_vietnam_district( element, selected ) {
        var thisParent = $(element).parents('.acf-input'),
            maqh = selected;
        var village = thisParent.find('select.acf_vietnam_village');
        if(maqh == ''){
            village.html('').select2({
                width : '100%',
                placeholder: village.data('placeholder'),
            });
        }else if(maqh && !loading_billing){
            loading_billing = true;
            $.ajax({
                type : "post",
                dataType : "json",
                url : devvn_acf_vn.admin_ajax,
                data : {action: "load_diagioihanhchinh", maqh : maqh, nonce : devvn_acf_vn.none_acfvn},
                context: this,
                success: function(response) {
                    village.html('').select2({
                        width : '100%',
                        placeholder: village.data('placeholder'),
                    });
                    if(response.success) {
                        var listQH = response.data;
                        var newState = new Option('', '');
                        village.append(newState);
                        $.each(listQH,function(index,value){
                            var newState = new Option(value.name, value.xaid);
                            village.append(newState);
                        });
                        loading_billing = false;
                    }
                }
            });
        }
    }

	if( typeof acf.add_action !== 'undefined' ) {

		acf.add_action('ready append', function( $el ){

			// search $el for fields of type 'viet-nam-address'
			acf.get_fields({ type : 'viet-nam-address'}, $el).each(function(){

				initialize_field( $(this) );

			});

		});


	} else {


		$(document).on('acf/setup_fields', function(e, postbox){

			$(postbox).find('.field[data-field_type="viet-nam-address"]').each(function(){

				initialize_field( $(this) );

			});

		});


	}


})(jQuery);
