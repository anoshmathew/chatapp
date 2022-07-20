import React, { useState, useEffect } from 'react'

import { mainUser, contactsMessages, Message } from './generateFakeData'
import Avatar from './components/Avatar'
import ContactBox from './components/ContactBox'
import MessagesBox from './components/MessagesBox'
import ChatInputBox from './components/ChatInputBox'
import Search from './components/Search'
import Welcome from './components/Welcome'
import Axios from "axios";
import './App.css'

function App() {
    const [data, setData] = useState(contactsMessages)
    const [contactSelected, setContactSelected] = useState({})
    const [currentMessages, setCurrentMessages] = useState([])
    const [message, setMessage] = useState('')
    const [search, setSearch] = useState('')
    const [filteredContacts, setFilterContacts] = useState([])

    useEffect(() => {
        getmes()
      console.log(contactsMessages)
        const currContact = data.find((d) => d.contact.id === contactSelected.id)
        setCurrentMessages((currContact && currContact.messages) || [])
        filterContacts(data, search)
    }, [contactSelected, data, search])

    function getmes(){
        Axios.post("http://localhost:8080/Api.php?apicall=getmessage", {
            
        }).then((res) => {     
            
            console.log(res);        
            
    
        });
    }
    function pushMessage() {
        const index = data.findIndex((d) => d.contact.id === contactSelected.id)
        const newData = Object.assign([], data, {
            [index]: {
                contact: contactSelected,
                messages: [...data[index].messages, new Message(true, message, new Date())],
            },
        })

        setData(newData)
        setMessage('')
    }

    function filterContacts(data, search) {
        const result = data.filter(({ contact }) => {
            return !search || contact.name.toLowerCase().includes(search.toLowerCase())
        })
        setFilterContacts(result)
    }

    return (
        <div className="app">
            <aside>
                <header>
                    <Avatar user={mainUser} />
                </header>
                <Search search={search} setSearch={setSearch} />
                <div className="contact-boxes">
                    {filteredContacts.map(({ contact, messages }) => (
                        <ContactBox
                            contact={contact}
                            key={contact.id}
                            setContactSelected={setContactSelected}
                            messages={messages}
                        />
                    ))}
                </div>
            </aside>
            {contactSelected.id ? (
                <main>
                    <header>
                        <Avatar user={contactSelected} showName />
                    </header>
                    <MessagesBox messages={currentMessages} />
                    <ChatInputBox message={message} setMessage={setMessage} pushMessage={pushMessage} />
                </main>
            ) : (
                <Welcome />
            )}
        </div>
    )
}

export default App

