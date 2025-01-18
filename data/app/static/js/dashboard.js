function addMessagesToView(message_array, self_name) {
    message_array.forEach(element => {
        class_name = 'message-other';
        let msg = document.createTextNode(element.message);
        let name = document.createTextNode(element.name);

        timestamp = element.sent_date + 'T' + element.sent_time + ':00';
        date = Date.parse(timestamp);
        let time = document.createTextNode(new Date(date).toLocaleString());

        if (element.name === self_name) {
            class_name = 'message-self';
            name = document.createTextNode('Me');
        }

        let name_box = document.createElement('p');
        name_box.appendChild(name);
        name_box.classList.add('sender-name');

        let time_box = document.createElement('p');
        time_box.appendChild(time);
        time_box.classList.add('sender-time');

        let mesg_box = document.createElement('div');
        mesg_box.appendChild(msg);
        mesg_box.classList.add('sender-message');

        let timename_box = document.createElement('div');
        timename_box.appendChild(name_box);
        timename_box.appendChild(time_box);
        timename_box.classList.add('sender-timename');

        let box = document.createElement('div');
        box.appendChild(timename_box);
        box.appendChild(mesg_box);
        box.classList.add(class_name)

        document.getElementById('message-place').appendChild(box)
    });
}

document.getElementById('logout').addEventListener(
    "click",
    () => {
        window.location.href = 'logout';
    }
);

let time = new Date().toISOString();
time = null;

fetch("/fetch_messages", {
    method: "POST",
    mode: "same-origin",
    credentials: "same-origin",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({ 
        time: time
    })
    }).then((x) => x.json()).then((y) => addMessagesToView(y.messages, self_name));