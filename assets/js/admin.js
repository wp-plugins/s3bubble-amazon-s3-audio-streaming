 (function($){
					$("#colour").ColorPicker({
						onSubmit: function (hsb, hex, rgb, el) {
							$(el).val(hex);
							$(el).ColorPickerHide();
						},
						onBeforeShow: function () {
							$("#colour").val(this.value);
							$(this).ColorPickerSetColor(this.value);
				
						},
						onChange: function (hsb, hex, rgb) {
							$("#colour").val("#" + hex);
						}
					}).bind("keyup", function () {
						$(this).ColorPickerSetColor(this.value);
					});
					$("#fontcolour").ColorPicker({
						onSubmit: function (hsb, hex, rgb, el) {
							$(el).val(hex);
							$(el).ColorPickerHide();
						},
						onBeforeShow: function () {
							$("#fontcolour").val(this.value);
							$(this).ColorPickerSetColor(this.value);
				
						},
						onChange: function (hsb, hex, rgb) {
							$("#fontcolour").val("#" + hex);
						}
					}).bind("keyup", function () {
						$(this).ColorPickerSetColor(this.value);
					});
				$('.s3abgimage').click(function(){
					$('#bgimage').val($(this).attr('href'));
					return false;
				});
				})(jQuery);