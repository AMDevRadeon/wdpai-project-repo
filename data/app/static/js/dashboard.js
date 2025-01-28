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

        let title_line = document.createElement('div');
        title_line.setAttribute('class', 'title-line');

        let title = document.createElement('p');
        title.setAttribute('class', 'title');

        let title_count = document.createElement('p');
        title_count.setAttribute('class', 'title-count');

        title_line.appendChild(title);
        title_line.appendChild(title_count);
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

                person_line.appendChild(user);
                person_holder.appendChild(person_line);
            }
        });

        person_holder.style.height = `calc(${person_holder.childNodes.length}lh + ${(person_holder.childNodes.length + 1) * 0.3}em)`;
        title.appendChild(document.createTextNode(chatroom[0][0]));
        title_count.appendChild(document.createTextNode(`[${person_holder.childNodes.length}]`));


        contact.appendChild(person_holder);

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
            curr_contact.getElementsByClassName('persons')[0].innerHTML = contact.getElementsByClassName('persons')[0].innerHTML;
            curr_contact.getElementsByClassName('persons')[0].style.height = `calc(${contact.getElementsByClassName('persons')[0].childNodes.length}lh + ${(contact.getElementsByClassName('persons')[0].childNodes.length + 1) * 0.3}em)`;
            curr_contact.getElementsByClassName('title')[0].innerText = contact.getElementsByClassName('title')[0].innerText;
            curr_contact.getElementsByClassName('title-count')[0].innerText = contact.getElementsByClassName('title-count')[0].innerText;
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
                console.log(data, from_time);
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
        resizeTextarea(document.getElementById('message-input'));
        document.getElementById('message-letter-count').innerText = `${getTextareaLetterCount(document.getElementById('message-input'))}`;
        document.getElementById('message-letter-count').style.display = 'none';
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


function resizeTextarea(textarea) {
    textarea.style.height = "1.2lh";
    textarea.style.height = `calc(${textarea.scrollHeight}px)`;
}

function getTextareaLetterCount(textarea) {
    return textarea.value.length;
}

document.getElementById('message-input-container').addEventListener('click',
    () => {
        document.getElementById('message-input').focus();
    }
)

document.getElementById('message-input').addEventListener('input',
    () => {
        resizeTextarea(document.getElementById('message-input'));
        let letter_count = getTextareaLetterCount(document.getElementById('message-input'));
        document.getElementById('message-letter-count').innerText = `${letter_count}`;
        if (letter_count >= 250) {
            document.getElementById('message-letter-count').style.display = 'inline-block';
        }
        else {
            document.getElementById('message-letter-count').style.display = 'none';
        }

        if (letter_count > 2000) {
            document.getElementById('message-letter-count').style.color = '#ff5151';
        }
        else {
            document.getElementById('message-letter-count').style.color = 'var(--platinum)';
        }
    }
)

document.getElementById('channel-display').addEventListener('click',
    () => {
        console.log(document.getElementById('contacts-list').className);
        if (document.getElementById('contacts-list').className === 'users-list-slide-in' || document.getElementById('contacts-list').className === '') {
            document.getElementById('contacts-list').className = 'users-list-slide-out';
            document.getElementById('channel-display').className = 'users-list-button-enabled';
        }
        else {
            document.getElementById('contacts-list').className = 'users-list-slide-in';
            document.getElementById('channel-display').className = 'users-list-button-disabled'
        }
    }
)


window.matchMedia('(max-width: 800px)').addEventListener('change', () => {
    document.getElementById('contacts-list').className = '';
});