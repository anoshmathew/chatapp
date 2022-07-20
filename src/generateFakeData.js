import React,{useState} from "react";
import Axios from "axios";
const faker = require('faker')

const current = new Date();

function Getmes()
{
    const [m, setm] = useState({})
    Axios.post("http://localhost:8080/Api.php?apicall=getmsg", {  
    }).then((res) => {            
        console.log(res);   
        setm(res)     
    });
}
var user1 = [{
            id: 123,
            name: "Anosh",
            avatar: "Nothing.com"
        },
        {
            id: 124,
            name: "Anosh2",
            avatar: "Nothing2.com"
        },
        {
            id: 125,
            name: "Glen",
            avatar: "Nothing2.com"
        }]

        //var userMes = m;
var userMes = [{
    id: 126,
    msg_id:124,
    msg: "Hellow",
    isMainUser: false,
    date : current
},
{
    id: 127,
    msg_id:124,
    msg: "Hellow Again",
    isMainUser: true,
    date : current
},
{
    id: 128,
    msg_id:125,
    msg: "Hey Glen",
    isMainUser: true,
    date : current
},
{
    id: 129,
    msg_id:125,
    msg: "Hi, Wassup",
    isMainUser: false,
    date : current
},
{
    id: 130,
    msg_id:125,
    msg: "Nothing Much",
    isMainUser: true,
    date : current
}]



export class Message {
    constructor(isMainUser, msg, date) {
        this.id = faker.random.uuid()
        this.msg = msg 
        this.isMainUser = isMainUser
        this.date = date 
    }
}

export const mainUser = user1;

//export const contacts = [...Array(5).keys()].map(() => new User())

export const contacts = user1;


/*export const contactsMessages = contacts.map((contact) => {
    return {
        contact,
        messages: [...Array(10).keys()]
            .map((_, i) => {
                return (i + 1) % 2 === 0 ? new Message(true) : new Message(false)
            })
            .filter((m) => m.msg),
    }
})
*/

export const contactsMessages = contacts.map((contact) => {
    return {
        contact,
        messages: userMes.filter(({msg_id}) => contact.id === msg_id)
    }
})
