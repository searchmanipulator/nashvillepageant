function collapseExpand(id)
{
	var elem = document.getElementById(id);

	if(elem.style.display == 'none'){
		elem.style.display = '';
	}else {
		elem.style.display = 'none';
	}
}