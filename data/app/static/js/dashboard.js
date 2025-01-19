function addMessagesToView(message_array, self_name) {
    message_array.forEach(element => {
        class_name = 'message-other';
        let msg = document.createTextNode(element.message);
        let name = document.createTextNode(element.name);

        date = Date.parse(element.sent_date);
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

        document.getElementById('message-place').prepend(box)
    });
}


function getActualMessages(self_name, from_time) {
    fetch("/fetch_messages", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            time: from_time.time
        })
    })
    .then(
        (response) => response.json()
    )
    .then(
        (data) => {
            if (data.messages.length !== 0) {
                last_elem = data.messages.slice(-1)[0];
                from_time.time = last_elem.sent_date;
                return addMessagesToView(data.messages, self_name);
            }
        }
    )
    .catch(
        (err) => console.log(err)
    )
    .finally(
        setTimeout(getActualMessages, 1000, self_name, from_time)
    )
}


function sendMessage(date_sent, message) {
    fetch("/send_messages", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            date_sent: date_sent,
            message: message
        })
    })
    .then(
        (response) => response.json()
    )
    .then(
        (data) => console.log(data)
    )
}


window.addEventListener('load', () => {
    from_time = {time: null};

    getActualMessages(self_name, from_time);    
})


document.getElementById('logout').addEventListener(
    "click",
    () => {
        window.location.href = 'logout';
    }
);

document.getElementById('message-button').addEventListener('click', 
    () => {
        let date_sent = new Date().toISOString();
        let message = document.getElementById('message-input').value;

        sendMessage(date_sent, message);

        document.getElementById('message-input').value = "";
    },
    true
)

// let [date_sent, time_sent] = new Date().toISOString().split('T');
// message = "Hello Mars, too!";

// fetch("/send_messages", {
//     method: "POST",
//     mode: "same-origin",
//     credentials: "same-origin",
//     headers: {
//         "Content-Type": "application/json"
//     },
//     body: JSON.stringify({ 
//         date_sent: date_sent,
//         time_sent: time_sent,
//         message: message
//     })
// })
// .then(
//     (response) => response.json()
// )
// .then(
//     (data) => console.log(data)
// )