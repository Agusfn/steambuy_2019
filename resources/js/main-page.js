

var lastUrl = "";
var validUrl = false;

var blacklistedGames = [
					["sub","28987","No es posible vender el GTA Complete."],
					["app", "730", "No es posible vender CS:GO (<a href='soporte/preguntas-frecuentes/#venta-csgo'>más info</a>)."],
					["sub", "54029", "No es posible vender CS:GO (<a href='soporte/preguntas-frecuentes/#venta-csgo'>más info</a>)."],
					["app", "383150", "No es posible vender el Dead Island"],
					["sub", "71952", "No es posible vender el Dead Island"],
					["app", "383180", "No es posible vender el Dead Island"],
					["sub", "71968", "No es posible vender el Dead Island"]
					];


$(document).ready(function(e) {
	
	//Chequear anchors
	/*if(window.location.hash) {
		var hash = window.location.hash.substring(1);
		if(hash == "formulario-juegos") {
			$("#game_form_modal").modal("show");
		}
	}*/



	$(".cpg-product").mouseenter(function(e) {
		$(this).children(".cpg-product-overlay").stop().animate({opacity:"0.35"}, 200, "swing");
		
		if($(this).hasClass("cpg-product-sm")) {
			$(this).children(".cpg-product-info").stop().animate({marginTop: "65px"}, 200, "swing");
		} else if($(this).hasClass("cpg-product-lg")) {
			$(this).children(".cpg-product-info").stop().animate({marginTop: "99px"}, 200, "swing");
		} else {
			$(this).children(".cpg-product-info").stop().animate({marginTop: "70px"}, 200, "swing");
		}
    });
	
	$(".cpg-product").mouseleave(function(e) {
		$(this).children(".cpg-product-overlay").stop().animate({opacity:"0"}, 200, "swing");
		if($(this).hasClass("cpg-product-sm")) {
			$(this).children(".cpg-product-info").stop().animate({marginTop: "105px"}, 200, "swing");
		} else if($(this).hasClass("cpg-product-lg")) {
			$(this).children(".cpg-product-info").stop().animate({marginTop: "149px"}, 200, "swing");
		} else {
			$(this).children(".cpg-product-info").stop().animate({marginTop: "112px"}, 200, "swing");
		}
    });	
	

	$(".catalog-panel .carousel").each(function(index, element) {
		update_catalog_slider_pagination($(this));
    });
	
	$(".catalog-panel .carousel").on('slid.bs.carousel', function () {
		update_catalog_slider_pagination($(this));
	})



    $("input[name=game_url]").on("change keyup", function() {

    	

    	if(lastUrl == $(this).val())
    		return;

    	lastUrl = $(this).val();
    	

		delay(function(){

			cleanSteamProductInfo();
			
			if(validateSteamUrl(lastUrl)) {

				$("#url_loading_spinner").show();
				$.ajax({

					data: {steam_url: lastUrl},
					url: "scripts/php/ajax_steam_game_data.php",
					type: "POST",

					success: function(response) {
						if(response.success) {
							$(".insert-url-label").hide();
							$("#game_name").text(response.data.product_name);
							$("#game_final_ars_price").text("$" + response.data.product_steambuy_finalprice + " ARS");
							$("#game_steam_real_price").text("$" + response.data.product_steam_finalprice + " ARS");
							if(response.data.product_discount)
								$("#limited_time_offer_warning").show();
						}
						else {
							alert(response.error_text);
						}
					},
					error: function(jqXhr, textStatus, errorMessage) {
						//console.log(jqXhr, textStatus, errorMessage);
						alert("Ocurrió un error procesando la URL del juego.");
					},
					complete:function() {
						$("#url_loading_spinner").hide();
					}
				});


			}

		}, 600);

    });



    $("#buy_form_submit").click(function() {

    	if($("#url_loading_spinner").is(":visible"))
    		return;

		if(validateBuyForm()) {
			if(!$("#terms_checkbox").is(":checked")) {
				alert("Acepta los términos y condiciones para continuar.");
				return;
			}
			$("#buy_game_form").submit();
		}
    });


    // Check if we have a steam URL get param
	var url = new URL(window.location.href);
	var steamurl = url.searchParams.get("steamurl");
	if(steamurl && validSteamUrl(steamurl)) {
		$("input[name=game_url]").val(steamurl).trigger("change");
	}

  
});



function validateBuyForm()
{
	cleanBuyFormErrors();

	var success = true;

	var buyerNameInput = $("input[name=buyer_name]");

	if(buyerNameInput.val() == "") {
    	buyerNameInput.parent().addClass("has-error");
    	buyerNameInput.next(".help-block").text("Ingresa el nombre y apellido").show();
    	success = false;
	}


	var buyerEmailInput = $("input[name=buyer_email]");

	if(!validateEmail(buyerEmailInput.val())) {
    	buyerEmailInput.parent().addClass("has-error");
    	buyerEmailInput.next(".help-block").text("Ingresa un e-mail válido").show();
    	success = false;
	}


	var gameUrlInput = $("input[name=game_url]");

	if(!validUrl) {
    	gameUrlInput.parent().addClass("has-error");
    	gameUrlInput.next(".help-block").text("Ingresa una URL de juego válida").show();
    	success = false;
	}


	var profileUrlInput = $("input[name=buyer_account_url]");

	var patt1 = /^(https?:\/\/)?steamcommunity.com\/id\/[a-z0-9]{1,50}(\/.*)?$/gi;
    var patt2 = /^(https?:\/\/)?steamcommunity.com\/profiles\/[0-9]{13,25}(\/.*)?$/gi;

    if( !patt1.test(profileUrlInput.val()) && !patt2.test(profileUrlInput.val()) ) {
    	profileUrlInput.parent().addClass("has-error");
    	profileUrlInput.next(".help-block").text("Ingresa una URL de cuenta Steam válida").show();
    	success = false;
    }

	return success;
}


function cleanBuyFormErrors()
{
	$("#buy_game_form .form-group").removeClass("has-error");
	$("#buy_game_form .form-group .help-block").text("");
}


function validateSteamUrl(steamUrl)
{
	var pattern = /^(https?:\/\/)?store\.steampowered\.com\/(app|sub|bundle)\/([0-9]{1,10})(\/.*)?$/i;

	var errorMsg = "";

    if(!pattern.test(steamUrl)) {
    	errorMsg = "La URL no es de un producto válido de Steam.";
    }
    else {
    	var urlInfo = pattern.exec(steamUrl);

    	if(urlInfo[2] == "bundle") {
    		errorMsg = "No es posible vender bundles de Steam.";
    	}
    	else {
    		var blackListedError = checkBlacklistedGame(urlInfo[2], urlInfo[3]);

    		if(blackListedError) {
    			errorMsg = blackListedError;
    		} 
    	}	
    }

    var urlInput = $("input[name=game_url]");

    if(errorMsg != "") {
    	urlInput.parent().addClass("has-error");
    	urlInput.next(".help-block").html(errorMsg).show();
    	validUrl = false;
    }
    else {
    	urlInput.parent().removeClass("has-error");
    	urlInput.next(".help-block").hide();
    	validUrl = true;
    }

    return validUrl;
}


function cleanSteamProductInfo()
{
	$(".insert-url-label").show();
	$("#game_name").text("");
	$("#game_final_ars_price").text("");
	$("#game_steam_real_price").text("");
	$("#limited_time_offer_warning").hide();
}


/**
 * Check if a steam game is blacklisted from selling from any reason.
 * @param  {string} productType 'sub' or 'app'
 * @param  {int} number      id of sub or app
 * @return {string}             error message if blacklisted, false if not.
 */
function checkBlacklistedGame(productType, number) {

	for(var i = 0; i <  blacklistedGames.length; i++ ) {
		if(productType == blacklistedGames[i][0] && number == blacklistedGames[i][1]) {
			return blacklistedGames[i][2];	
		}
	}
	return false;
	
}


function update_catalog_slider_pagination(carousel) {
	var total_items = carousel.find(".item").length;
	var current_post = carousel.find(".item.active").index() + 1;
	carousel.closest(".catalog-panel").find(".cp-carousel-pagination").text(current_post + "/" + total_items);
}

function n_round(number,decimals)
{
	return Math.round(number * Math.pow(10, decimals)) / Math.pow(10, decimals);
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

function startsWith(str, prefix) {
    return str.lastIndexOf(prefix, 0) === 0;
}


var delay = (function(){
  var timer = 0;
  return function(callback, ms){
  clearTimeout (timer);
  timer = setTimeout(callback, ms);
 };
})();


function validSteamUrl(url) {
	var pattern = /^(https?:\/\/)?store\.steampowered\.com\/(app|sub|bundle)\/([0-9]{1,10})(\/.*)?$/i;
	return pattern.test(url);
}