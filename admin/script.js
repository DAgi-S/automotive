const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

const sidebarDeactivator = document.getElementById('sidebar-deactivator');
const closeBtn = document.getElementById('close_btn');


closeBtn.addEventListener('click',()=>{
    sidebar.classList.add('hide'); 
	closeBtn.style.display = "none";
	sidebarDeactivator.style.opacity = '0';
	setTimeout(() => {
        sidebarDeactivator.style.display = 'none';
      }, 300); 
});

sidebarDeactivator.addEventListener('click',()=>{
    sidebar.classList.add('hide'); 
	closeBtn.style.display = "none";
    sidebarDeactivator.style.opacity = '0';
    setTimeout(() => {
        sidebarDeactivator.style.display = 'none';
      }, 300); // Delay hiding the overlay for smooth transition
    });

/*menuBar.addEventListener('click',()=>{
		sidebar.classList.toggle('hide'); 
		closeBtn.style.display = "block";
		sidebarDeactivator.style.display = "block";
		sidebarDeactivator.style.opacity = '0.5';
	});*/

	menuBar.addEventListener('click', function (e) {
		if(window.innerWidth > 768) {
			sidebar.classList.toggle('hide'); 
			closeBtn.style.display = "none";
			sidebarDeactivator.style.display = "none";
			sidebarDeactivator.style.opacity = '0';
			} else {
				sidebar.classList.toggle('hide'); 
				closeBtn.style.display = "block";
				sidebarDeactivator.style.display = "block";
				sidebarDeactivator.style.opacity = '0.5';
			}
		}
	)
	


allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});




// TOGGLE SIDEBAR



function checkWidth() {
    if (window.innerWidth < 768) {
        sidebar.classList.add('hide'); 
		closeBtn.style.display = "none";
		sidebarDeactivator.style.display = "none";
		sidebarDeactivator.style.opacity = '0';
    } else {
        sidebar.classList.remove('hide'); 
		closeBtn.style.display = "none";
		sidebarDeactivator.style.display = "none";
		sidebarDeactivator.style.opacity = '0';
    }
}

// Check width on load
window.onload = checkWidth;

// Check width on resize
window.onresize = checkWidth;




const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if(window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if(searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})




if(window.innerWidth < 768) {
	sidebar.classList.remove('hide');
} else if(window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if(this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})



const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
})


/*const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});




// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})







const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if(window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if(searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})





if(window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if(window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if(this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})



const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
})*/