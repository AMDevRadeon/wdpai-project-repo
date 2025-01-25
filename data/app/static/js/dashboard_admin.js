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

function getChatrooms() {
    fetch("/chatrooms", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            reason: "get_chatrooms"
        })
    })
    .then(
        (response) => response.json()
    )
    .then(
        (data) => {
            addChatroomsToView(data.chatrooms);
        }
    )
    .finally (
        setTimeout(getChatrooms, 1000)
    )
}

function addChatrooms() {
    fetch("/chatrooms", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            reason: "add_chatrooms"
        })
    })
    .then(
        (response) => response.json()
    )
    .then(
        (data) => {
            console.log(data);
        }
    )
}

function deleteChatrooms(chatroom_id) {
    fetch("/chatrooms", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            reason: "del_chatrooms",
            chatroom: chatroom_id,
        })
    })
    .then(
        (response) => response.json()
    )
    .then(
        (data) => {
            console.log(data);
        }
    )
}


async function addUserToChatroom(chatroom_id, user_name) {
    return fetch("/chatrooms", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            reason: "add_users",
            chatroom: chatroom_id,
            user: user_name
        })
    })
    .then(
        (response) => response.json()
    );
}

function deleteUserFromChatroom(chatroom_id, user_name) {
    fetch("/chatrooms", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            reason: "del_users",
            chatroom: chatroom_id,
            user: user_name
        })
    })
    .then(
        (response) => response.json()
    )
    .then(
        (data) => {
            console.log(data);
        }
    )
}


function addChatroomsToView(chatrooms)
{
    let keys = chatrooms.map((element) => { return element.id });
    let uniq_keys = keys.filter((v, i, a) => { return a.indexOf(v) === i });
    let substituted_contacts = Array();

    uniq_keys.forEach(element => {
        let chatroom = chatrooms.filter((v, i ,a) => { return v.id === element });

        let contact = document.createElement('div');
        contact.setAttribute('id', 'contact_' + element);
        contact.setAttribute('class', 'contacts-list-contact');

        let title_line = document.createElement('p');
        title_line.setAttribute('class', 'title-line');

        let chatroom_del = document.createElement('div');
        chatroom_del.setAttribute('class', 'chatroom-deleter');

        let title = document.createElement('p');
        title.setAttribute('class', 'title');
        title.appendChild(document.createTextNode('Private chat'));

        title_line.appendChild(title);
        title_line.appendChild(chatroom_del);
        contact.appendChild(title_line);

        let person_holder = document.createElement('div');
        person_holder.setAttribute('class', 'persons');

        chatroom.forEach(person => {
            if (person.name !== null) {
                let person_line = document.createElement('div');
                person_line.setAttribute('class', 'person-line');

                let user = document.createElement('p');
                user.setAttribute('class', 'person');
                user.appendChild(document.createTextNode(person.name));

                let user_del = document.createElement('div');
                user_del.setAttribute('class', 'person-deleter');
                user_del.addEventListener('click',
                    (event) => {
                        event.stopPropagation();
                        deleteUserFromChatroom(element, person.name);
                    }
                );

                person_line.appendChild(user);
                person_line.appendChild(user_del);

                person_holder.appendChild(person_line);
            }
        });

        person_holder.style.height = `calc(${person_holder.childNodes.length}lh + ${(person_holder.childNodes.length + 1) * 0.3}em)`;

        contact.appendChild(person_holder);

        let person_adder = document.createElement('div');
        person_adder.setAttribute('class', 'add-person');
        let person_adder_input = document.createElement('input');
        person_adder_input.setAttribute('class', 'add-person-input');
        let person_adder_send = document.createElement('div');
        person_adder_send.setAttribute('class', 'add-person-send');
        person_adder.appendChild(person_adder_input);
        person_adder.appendChild(person_adder_send);

        contact.appendChild(person_adder);

        contact.addEventListener('click',
            (event) => {
                console.log(event.target);

                if (request_type.room !== element) {
                    request_type.request = "private";
                    request_type.room = element;
                    request_type.time = null;
        
                    document.getElementById('message-place').innerHTML = '';

                    Array.from(document.querySelectorAll('div.contacts-list-contact')).forEach(
                        (el) => {
                            el.classList.remove('contacts-list-contact-enabled');
                        }
                    );

                    contact.classList.add('contacts-list-contact-enabled');
                }
        });

        person_adder.addEventListener('click',
            (event) => {
                event.stopPropagation();
            }
        );

        person_adder_send.addEventListener('click',
            () => {
                addUserToChatroom(element, person_adder_input.value)
                .then(x => console.log(x));
                person_adder_input.value = "";
            }
        )

        substituted_contacts.push(contact);
    });

    let chats = Array.from(document.querySelectorAll('#contacts-list > :not(#contact_global, #contacts-add-contact)'));

    substituted_contacts.forEach((contact) => {
        let curr_contact = undefined;

        if (chats !== undefined) {
            curr_contact = chats.find(x => x.id === contact.id);
        }

        if (curr_contact === undefined) {
            document.getElementById('contacts-list').appendChild(contact);
        }
        else {
            curr_contact.getElementsByClassName('persons')[0].replaceChildren(...contact.getElementsByClassName('persons')[0].childNodes);
            curr_contact.getElementsByClassName('persons')[0].style.height = `calc(${curr_contact.getElementsByClassName('persons')[0].childNodes.length}lh + ${(curr_contact.getElementsByClassName('persons')[0].childNodes.length + 1) * 0.3}em)`;
            chats.splice(chats.indexOf(curr_contact), 1);
        }
    });

    if (chats.length > 0) {
        chats.forEach((contact) => {
            contact.remove();
        })
    }
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
            request: from_time.request,
            room_id: from_time.room,
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
                console.log(last_elem, from_time);
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


function sendMessage(date_sent, message, from_time) {
    fetch("/send_messages", {
        method: "POST",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            request: from_time.request,
            room_id: from_time.room,
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


// Very important!!!
request_type = {
    request: "global",
    room: 0,
    time: null
};


window.addEventListener('load', () => {
    getChatrooms();
    getActualMessages(self_name, request_type);    
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

        sendMessage(date_sent, message, request_type);

        document.getElementById('message-input').value = "";
    },
    true
)


document.getElementById('contact_global').addEventListener('click',
    () => {
        if (request_type.request != "global") {
            request_type.request = "global";
            request_type.room = 0;
            request_type.time = null;

            document.getElementById('message-place').innerHTML = '';

            Array.from(document.querySelectorAll('div[id^=\'contact_\']')).forEach(
                (el) => {
                    el.classList.remove('contacts-list-contact-enabled');
                }
            );

            document.getElementById('contact_global').classList.add('contacts-list-contact-enabled');
        }
    }
)

document.getElementById('contacts-add-contact').addEventListener('click', 
    () => {
        addChatrooms();
});