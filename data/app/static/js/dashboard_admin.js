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
        (data) => addChatroomsToView(data.chatrooms)
    )
}

let coordinator = {endpoint: '/fetch_messages'}

function addChatroomsToView(chatrooms)
{
    let keys = chatrooms.map((element) => { return element.id });
    let uniq_keys = keys.filter((v, i, a) => { return a.indexOf(v) === i });

    uniq_keys.forEach(element => {
        let chatroom = chatrooms.filter((v, i ,a) => { return v.id === element });
        let contact = document.createElement('div');
        contact.setAttribute('id', 'contact_' + element);
        contact.setAttribute('class', 'contacts-list-contact');
        chatroom.forEach(person => {
            let user = document.createElement('p');
            user.setAttribute('class', 'person');
            user.appendChild(document.createTextNode(person.name));
            contact.appendChild(user);
        })
        document.getElementById('contacts-list').appendChild(contact);
    });
}