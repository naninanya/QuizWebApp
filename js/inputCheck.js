function inputCheck(form) {
	alert("hoge");
	var userIdP = document.getElementById("groupName");
	var passWordP = document.getElementById("pass");
	userIdP.innerHTML = "";
	passWordP.innerHTML = "";

	if (form.userId.value == "") {
		userIdP.innerHTML = "必須項目です。";
		return false;
	}
	if (form.passWord.value == "") {
		passWordP.innerHTML = "必須項目です。";
		return false;
	}
	return true;
}
