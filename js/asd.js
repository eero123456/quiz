let form = document.querySelector("#questions-wrapper form");

if (form !== null) {
	form.addEventListener("submit", addQuestion);
}

async function addQuestion(e) {
	e.preventDefault();
	console.log(e);

	let questionID = e.target[0].value;

	if (!questionID) {
		return;
	}

	let questionType = e.target[1].value;

	console.log(questionID, questionType);

	let res = await fetch("/quiz/addquestion.php", {
		method: "POST",
		body: new FormData(e.target),
		headers: { "X-Requested-With": "js" },
	});

	console.log(res);

	let asd = await res.text();
	//console.log(asd);

	insertQuestion(asd);
}

function insertQuestion(question) {
	let list = document.getElementById("question-list");
	list.insertAdjacentHTML("beforeend", question);
}

function deleteOption(e) {
	let tbody = e.target.closest("tbody");

	if (tbody.children.length > 4) {
		e.target.closest("tr").remove();
	}
}
function addOption(e) {
	let tbody = e.target.closest("tbody");

	let optionID = getNextOptionID(tbody);

	let previous = e.target.closest("tr").previousElementSibling;

	let newOptionTr = previous.cloneNode(true);

	let inputs = newOptionTr.querySelectorAll("input");
	inputs.forEach((input) => {
		input.name = input.name.replace(/option-\d+/, `option-${optionID}`);
	});

	previous.after(newOptionTr);
}

function getNextOptionID(table) {
	let inputs = table.querySelectorAll("input");
	let ids = [];
	for (let i = 0; i < inputs.length; i++) {
		let a = inputs[i].name.match(/option-(\d+)/);
		if (a) {
			ids.push(parseInt(a[1]));
		}
	}

	return Math.max(...ids) + 1;
}

async function deleteQuestion(event, questionID) {
	let quizID = parseInt(document.querySelector('input[name="quizID"]').value);

	let data = new FormData();
	data.append("quizID", quizID);
	data.append("questionID", questionID);

	let response = await fetch("/quiz/deletequestion.php", {
		method: "POST",
		body: data,
		headers: { "X-Requested-With": "js" },
	});

	if (!response.ok) {
		console.error("Kysymyksen poistaminen epäonnistui", response);
		return;
	}

	let wrapper = event.target.closest("div.question");
	if (wrapper) {
		wrapper.style.transition = "opacity 1s ease";
		wrapper.style.opacity = 0;
		setTimeout(() => {
			wrapper.remove();
		}, 1000);
	}
}

async function uploadImage(event, questionID) {
	let quizID = parseInt(document.querySelector('input[name="quizID"]').value);

	let img = event.target.parentElement.querySelector("input").files[0];

	if (img === undefined) {
		return;
	}

	let data = new FormData();

	data.append("quizID", quizID);
	data.append("questionID", questionID);
	data.append("action", "add");
	data.append("image", img);

	let response = await fetch("/image.php", { body: data, method: "POST" });
	
	await response.text();
	
	if (!response.ok) {
		return;
	}
	
	let kuva=event.target.parentElement.querySelector("img")
	
	var reader = new FileReader();
	reader.onload = function(event) {
		kuva.src = event.target.result;
		kuva.title = img.name;
	};
	  
	reader.readAsDataURL(img);

}


async function clearImage(event, questionID) {
	
	let quizID = parseInt(document.querySelector('input[name="quizID"]').value);
	
	let data = new FormData();

	data.append("quizID", quizID);
	data.append("questionID", questionID);
	data.append("action", "clear");	

	let response = await fetch("/image.php", { body: data, method: "POST" });
	
	await response.text();
	
	if (!response.ok) {
		return;
	}

	event.target.parentElement.querySelector("img").src="";
	
}