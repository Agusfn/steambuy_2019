
var last_calc_query = new Array();
last_calc_query[0] = 0;
last_calc_query[1] = 0;



// Variables formulario de compra

var game_form_status = 0; // 0 = ingreso de datos, 1 = confirmación datos y medio de pago, -1 = cargando, 2=generado
var gf_error_text = "";

var gf_client_name, gf_client_email, gf_client_steamurl, gf_product_name, gf_product_sellingsite, gf_product_siteurl, gf_product_price, gf_product_discount;

var gf_payment_method = 1;

var gf_product_prices = new Array();

/********************/


var slider_timer;


var blocked_games = [["app","730","El CS:GO no está a la venta desde el formulario de compras. <a href='https://www.facebook.com/steambuy/posts/657170941110751' target='_blank'>Más info</a>."],
					["sub","28987","No es posible comprar el GTA Complete debido a que no es posible enviarlo en formato Steam Gift desde Steam."],
					["app","570660","No es posible comprar el Binding of Isaac Afertbirth+ debido a que no es posible enviarlo a en formato Steam Gift desde Steam."]];


$(document).ready(function(e) {
	
	//Chequear anchors
	if(window.location.hash) {
		var hash = window.location.hash.substring(1);
		if(hash == "formulario-juegos") {
			$("#game_form_modal").modal("show");
		}
	}


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




   // ***** FORMULARIO DE COMPRA DE JUEGOS *****
   
   $("#gf_button_confirm").click(function(e) {
    	
		if(game_form_status == 0) 
		{
			
			if(!validate_game_form()) return false;
			
			$("#game_form_modal").data('bs.modal').isShown = false;
			$("#gf_loadicon").css("display","inline-block");
			
			game_form_status = -1;
			$.ajax({
				data:  {"price_usd" : gf_product_price},
				url:   'scripts/php/ajax_get_prices.php',
				type:  'post',
													
				success:  function (response) {
					
					console.log(response);
							
					price_response = JSON.parse(response);
					
					if(price_response["success"] == true) { 

						// Paso 2 de formulario
						
						$("#gf_error_list").slideUp('fast')

						gf_product_prices = price_response["prices"];
						
						//Completando datos de confirmacion..
						$("#gf_arsprice1").text("$ " + gf_product_prices[0] + " ARS");
						$("#gf_arsprice2").text("$ " + gf_product_prices[1] + " ARS");
						$("#gf_sf_confirmation_name").text(gf_client_name);
						$("#gf_sf_confirmation_email").text(gf_client_email);
						$("#gf_sf_confirmation_gamename").text(gf_product_name);
						
						if(gf_product_sellingsite == 1) $("#gf_sf_confirmation_gamesite").text("Steam");
						else if(gf_product_sellingsite == 2) $("#gf_sf_confirmation_gamesite").text("Amazon");
						
						$("#gf_sf_confirmation_gameurl").attr("value",gf_product_siteurl);
						$("#gf_sf_confirmation_gameprice").text("USD " + parseFloat(gf_product_price));
						
						if(gf_product_discount == 0) $("#gf_sf_confirmation_gameoffer").text("No");
						else if(gf_product_discount == 1) $("#gf_sf_confirmation_gameoffer").text("Sí");
						
						//transicion de formularios y cambio de tamaño
						$("#gf_first_form").fadeOut("slow", function() { 
							$("#gf_second_form").fadeIn("slow"); 
							$("#game_form_modal").data('bs.modal').isShown = true;
						});
						
						$("#game_form_modal .modal-dialog").animate({width:"700px"}, "slow");
						$("#game_form_modal .modal-body").animate({height:"325px"}, "slow");
						
							
						$("#gf_button_confirm").html("Generar cupón&nbsp;&nbsp;<span class='glyphicon glyphicon-barcode'></span>");
						$("#gf_button_cancel").text("Cancelar compra");
						$("#gf_order_price").show();
						$("#gf_order_price").html("<strong>Total:</strong> $" + gf_product_prices[0] + " ARS");
						
						game_form_status = 1;
					
					} else {
						$("#gf_error_list ul").html("Error obteniendo precios");
						$("#gf_error_list").slideDown('slow');
					}
					
					
					$("#gf_loadicon").css("display","none");
				}
			});


		} else if(game_form_status == 1) {
			
			game_form_status = -1;
			
			$("#game_form_modal").data('bs.modal').isShown = false;
			
			$.ajax({
				data:{"client_name":gf_client_name, "client_email":gf_client_email, "client_steam_url":"",  "product_name":gf_product_name, 
				"product_sellingsite":gf_product_sellingsite, "product_siteurl":gf_product_siteurl, "product_usdprice":gf_product_price, 
				"product_discount":gf_product_discount, "payment_method":gf_payment_method, "remember_data":gf_remember_data},
				
				url:"scripts/php/ajax_order_generator.php",
				type:"POST",
				
				beforeSend: function() {
					$("#gf_loadicon").css("display","inline-block");
				},
				success: function(response) {
					
					console.log(response);
					if(!startsWith(response, "Error")) {
						var orderData = JSON.parse(response);
						game_form_status = 2;
						
						if(gf_payment_method == 1) {
							$(".gf_tf_transferinstructions").hide();
							$(".gf_tf_transferdata").hide();
							
							$(".gf_tf_ticketinstructions").show();
							$(".gf_tf_ticketdata").show();
							$("#gf_tf_ticket_button").attr("href",orderData["order_purchaseticket"]);
							var split1 = orderData["order_purchaseticket"].split("?id=");
							$("#gf_tf_ticketformat").attr("href","https://www.cuentadigital.com/ticket.php?id=" + split1[1].substr(4, 8) + "&l=es");
								
						} else if(gf_payment_method == 2) {
							$(".gf_tf_ticketinstructions").hide();
							$(".gf_tf_transferinstructions").show();
							$(".gf_tf_ticketdata").hide();
							$(".gf_tf_transferdata").show();
							
							$("#gf_tf_bank_account").text(orderData["bank_account"]);
							$("#gf_tf_bank_account_cbu").text(orderData["bank_account_cbu"]);
							$("#gf_tf_bank_account_owner").text(orderData["bank_account_owner"]);
							$("#gf_tf_bank_account_cuil").text(orderData["bank_account_cuil"]);
						}
						
						if(gf_product_discount == 1) {
							$(".gf_tf_offerwarning").show();
							$("#gf_tf_site_url").attr("href", gf_product_siteurl);
						}
						
						$(".gf_tf_gamearsprice").text("$" + gf_product_prices[gf_payment_method - 1] + " ARS");
						$(".gf_tf_gamename").text(gf_product_name);
						
						$("#gf_tf_orderid").text(orderData["order_id"]);
						$("#gf_tf_orderpass").text(orderData["order_password"]);
						$("#gf_tf_clientemail").text(gf_client_email);
						
						if(orderData["mailsent"] == 0) $("#gf_tf_mailerror").css("display","block");
						
						$("#gf_order_price").hide();
						$("#gf_button_confirm").hide();
						$("#gf_button_cancel").html("Cerrar");
						
						$("#gf_second_form").fadeOut("slow", function() { 
							$("#gf_third_form").fadeIn("slow"); 
							$("#game_form_modal").data('bs.modal').isShown = true;
						});
						$("#game_form_modal .modal-body").animate({height:$("#gf_third_form").height() + 40}, "slow");

					} else {
						game_form_status = 1;
						alert(response);	
					}
					$("#gf_loadicon").css("display","none");
				},
				error: function() {
					$("#gf_loadicon").css("display","none");
				}
			});
		}
		
		
	});
   

	
	$("#gf_input_gamediscount").change(function(e) {
		if($(this).prop("selectedIndex") == 0) {
			$("#gf_offer_warning").slideUp("slow");
		} else if($(this).prop("selectedIndex") == 1) {
			$("#gf_offer_warning").slideDown("slow");
		}
	});
	
	/** 2º paso **/
	
	$("#gf_paymentoptions .list-group-item").click(function(e) {
		if(game_form_status == 1) {
			$("#gf_paymentoptions").children().removeClass("active");
			$(this).addClass("active");
			$("#gf_order_price").html("<strong>Total:</strong> $" + gf_product_prices[$(this).index()] + " ARS");
			
			gf_payment_method = $(this).index() + 1;
			
		  	if($(this).index() == 0) {
			   $("#gf_button_confirm").html("Generar cupón&nbsp;&nbsp;<span class='glyphicon glyphicon-barcode'></span>");
		   	} else if($(this).index() == 1) {
			   $("#gf_button_confirm").html("Generar pedido");
	   		}
		}
		
    });
	
	// al cerrar el formulario
	
	$("#game_form_modal").on('hidden.bs.modal', function (e) {
		
		if(game_form_status == 1 || game_form_status == 3) {
			$("#gf_first_form input").val("");
			
			if(game_form_status == 1) {
				$("#gf_second_form").hide();
				$("#gf_order_price").hide();
			} else if(game_form_status == 2) {
				$("#gf_third_form").hide();
				$("#gf_button_confirm").show();
			}
			if(gf_remember_data == true) {
				$("#gf_input_name").val(gf_client_name);
				$("#gf_input_email").val(gf_client_email);
				//$("#gf_input_userurl").val(gf_client_steamurl);
			}
			
			game_form_status = 0;
			gf_payment_method = 1;
			
			$("#gf_first_form select").prop("selectedIndex", 0);
			$("#gf_input_sellingsite").trigger("change");
			
			$("#gf_offer_warning").hide();
			$("#gf_sf_repeatwarning").hide();
			$("#gf_paymentoptions").children().removeClass("active");
			$("#gf_paymentoptions .list-group-item:first").addClass("active");
			$("#gf_first_form").show();
			$("#gf_button_confirm").html("Continuar");
			$("#gf_button_cancel").html("Cerrar");
			
			$("#game_form_modal .modal-dialog").css("width","600px");
			$("#game_form_modal .modal-body").css("height", "auto");
		}
	});

   // ***** CALCULADORA DE PRECIOS *****
   
   
   
   $("#calcbox_btn").click(function() {
		var ammount = $("#calcbox_input").val();
		if (ammount == "0.00" || ammount == 0 || isNaN(ammount)) {
			$(".calcbox_placeholder").show();
			$(".calcbox_placeholder").text("Ingresa el precio en dólares");
			$("#calcbox_input").val("0.00");
        	return;
		} else if(ammount > 50) {
			$(".calcbox_placeholder").show();
			$(".calcbox_placeholder").text("Ingresa un valor de hasta 50 usd");
			return;
		}
		ammount = parseFloat(ammount);
		if(ammount == last_calc_query[0]) {
			$(".calcbox_usdammount").text(last_calc_query[0] + " USD :");
			$(".calcbox_arsresponse").text("$" + last_calc_query[1] + " ARS");
			$(".calcbox_usdammount").show();
			$(".calcbox_arsresponse").show();
		} else {
			var parametros = {"price" : ammount, "pay_method" : 1};
			$.ajax({
				data:  parametros,
				url:   'global_scripts/php/ajax_output_prices.php',
				type:  'post',
													
				beforeSend: function () {
					$("#calcbox_loadicon").show();
				},
				success:  function (response) {
					$("#calcbox_loadicon").hide();
					if(response.indexOf("error") == -1){
						last_calc_query[0] = ammount;
						last_calc_query[1] = response;
						$(".calcbox_usdammount").text(ammount + " USD :");
						$(".calcbox_arsresponse").text("$" + response + " ARS");
						$(".calcbox_usdammount").show();
						$(".calcbox_arsresponse").show();
					}else{
						$(".calcbox_placeholder").show();
						$(".calcbox_placeholder").text(response);
					}
				}
			});
		}   
	});

	$("#calcbox_input").keypress(function(e) {
        var key = e.keyCode || e.which;
		if(key == 13) {
			$("#calcbox_btn").click(); // Se llama al evento como si se apretase el botón
		}
    });
	
	$("#calcbox_input").focus(function() {
		$(".calcbox_placeholder").hide();	
		$(".calcbox_usdammount").hide();
		$(".calcbox_arsresponse").hide();
	});
	
	$("#calcbox_input").blur(function () {
		if($(this).val() == 0) {
			$(".calcbox_placeholder").show();
			$(".calcbox_placeholder").text("Ingresa el precio en dólares");	
		}
	});
  
});



function validate_game_form() {

	error_text = "";
			
	$("#gf_input_name").val(toTitleCase($("#gf_input_name").val().trim().replace(/\s+/g, " ")));
	gf_client_name = $("#gf_input_name").val();
	
	gf_client_email = $("#gf_input_email").val();
	//gf_client_steamurl = $("#gf_input_userurl").val();
	
	$("#gf_input_gamename").val($("#gf_input_gamename").val().replace(/\s+/g, " "));
	gf_product_name = $("#gf_input_gamename").val();
	
	gf_product_sellingsite = 2;
	gf_product_siteurl = $("#gf_input_gameurl").val();
	gf_product_price = $("#gf_input_gameprice").val();
	
	gf_product_discount = $("#gf_input_gamediscount").prop("selectedIndex");
	gf_remember_data = $("#gf_input_rememberdata").prop("checked");
	
	
	if(gf_client_name == 0) {
		error_text += "<li>Ingresa tu nombre y apellido</li>";
	} else if(gf_client_name.length < 5) {
		error_text += "<li>Ingresa un nombre de al menos 5 caracteres</li>";
	} else if(!(/^[a-z\sñáéíóú]*$/i.test(gf_client_name))) {
		error_text += "<li>Ingresa un nombre y apellido con caracteres válidos</li>";
	}

	if(gf_client_email == 0) {
		error_text += "<li>Ingresa tu correo electrónico</li>";
	} else if(!validateEmail(gf_client_email)) {
		error_text += "<li>Ingresa un correo electrónico válido</li>";
	}
	
	
	/*if($("#gf_input_sellingsite").prop("selectedIndex") == 0) {
		var patt1 = /^(https?:\/\/)?steamcommunity.com\/id\/[a-z0-9]{1,50}(\/.*)?$/gi;
		var patt2 = /^(https?:\/\/)?steamcommunity.com\/profiles\/[0-9]{13,25}(\/.*)?$/gi;
		if( !patt1.test(gf_client_steamurl) && !patt2.test(gf_client_steamurl) ) {
			error_text += "<li>La SteamURL ingresada no es válida. Puedes ver tu url con <a href='http://steamidfinder.com/' target='_blank'>esta herramienta</a>.</li>";	
		}
	}*/
				
	var re_game = /(2|two|3|three|4|four|6|six)(\s|\-)?pack/i;
	if(gf_product_name == 0) error_text += "<li>Ingresa el nombre del juego</li>";
	else if(re_game.test(gf_product_name)) {
		error_text += "<li>No es posible vender packs múltiples (4pack, etc) por limitaciones de Steam, disculpa las molestias.</li>";
	}
	
	if(gf_product_siteurl == 0) error_text += "<li>Ingresa la URL de la tienda del juego</li>";
	else if(!gf_product_siteurl.match(/[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi)) {
		error_text += "<li>Ingresa una URL de producto válida</li>";
	} else {
		
		if(gf_product_sellingsite == 1) {
			
			var pattern = /^(https?:\/\/)?store\.steampowered\.com\/(app|sub)\/([0-9]{1,10})(\/.*)?$/i;
			
			if(!pattern.test(gf_product_siteurl)) { // Si no es de steam
				error_text += "<li>La URL de Steam ingresada es inválida. Ejemplo de URL: http://store.steampowered.com/app/730/ </li>";
				if(gf_product_siteurl.indexOf("/bundle/") !== -1) error_text += "<strong>Los /bundles/ nuevos de Steam NO pueden ser vendidos debido a que no pueden ser enviados en formato Steam Gift</strong>";
			} else {
				var matches = pattern.exec(gf_product_siteurl);
	
				blocked = is_steamgame_blocked(matches); // chequea juegos que no se puedan vender
				if(blocked != false) error_text += "<li>"+blocked+"</li>";
			}

		} else if(gf_product_sellingsite == 2) {
			if(gf_product_siteurl.indexOf("amazon.com") == -1) error_text += "<li>La URL ingresada no parece ser de Amazon.com</li>";
		}	
	}	
	
	if(isNaN(gf_product_price) || gf_product_price == 0) {
		error_text += "<li>Ingresa el precio del juego en dólares</li>";
	} else if(gf_product_price > 50) {
		error_text += "<li>El valor no puede superar los 50 USD. Contactanos para consultar si podemos venderlo.</li>";
	}
	
	if(error_text === "") {
		return true;
	} else {
		$("#gf_error_list ul").html(error_text);
		$("#gf_error_list").slideDown('slow');
		return false;	
	}
		
}



function is_steamgame_blocked(matches) {

	for(var i = 0; i <  blocked_games.length; i++ ) {
		if(matches[2] == blocked_games[i][0] && matches[3] == blocked_games[i][1]) {
			return blocked_games[i][2];	
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
