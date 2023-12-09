
function setStatusFinished() {
	var x = document.querySelectorAll("select[name*=\'status\']");
	var i;
	for (i = 0; i < x.length; i++) {
		x[i].value = "2";
	}
}

const btn = document.getElementById('btnSetStatus');
btn.addEventListener('click', setStatusFinished)

console.info('Match edit module initialized.');
