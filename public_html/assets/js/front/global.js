
const burger = document.querySelector(".burger");
const menu = document.querySelector(".menu");
const mediaQuery = window.matchMedia("(min-width: 990px)");

/*nav fixe au scroll + changement de background color*/
/*media queries*/


const navScroll = () => {
	let linkItems = document.querySelectorAll(".link-item");
	const navbar = document.querySelector('#navbar');
	window.onscroll = () => {
	  if (document.documentElement.scrollTop >= 100 && mediaQuery.matches) {
		navbar.style.backgroundColor = '#ffff';
		navbar.style.padding = '0';
		for (let linkItem of linkItems) {
			linkItem.style.color = "#121b22";
			}
	  } else {
		navbar.style.backgroundColor = 'transparent';
		navbar.style.padding = '1rem';
		for (let linkItem of linkItems) {
			linkItem.style.color = "#ffff";
			}
	  }
	}
  }
  navScroll();

const animBurger = () => {
	burger.addEventListener("click", () => {
		/* toggle Nav*/
		menu.classList.toggle("nav-active");
		/* animation burger*/
		burger.classList.toggle("toggle");
		
	});
};
animBurger();

$(document).ready(function() {
	toastr.options = {
		  'closeButton': true,
		  'debug': false,
		  'newestOnTop': true,
		  'progressBar': true,
		  'positionClass': 'toast-top-right',
		  'preventDuplicates': false,
		  'showDuration': '1000',
		  'hideDuration': '1000',
		  'timeOut': '5000',
		  'extendedTimeOut': '1000',
		  'showEasing': 'swing',
		  'hideEasing': 'linear',
		  'showMethod': 'fadeIn',
		  'hideMethod': 'fadeOut',
	  }
});

/************ contact mail enable button ************/
$(document).on('click', '#user_agreeTerms', function(){
	if($(this).prop('checked')) {
		$('#sendContactMail').attr('disabled', false);
	} else {
		$('#sendContactMail').attr('disabled', true);
	}
});

/************ contact mail errors ************/
$('.required').each(function(){
	var cible = $(this).next();
	cible.on('focusout', function(){
		if($(this).val() == '') {
			$(this).addClass('is-invalid');
		} else {
			$(this).removeClass('is-invalid');
		}
	});
});
