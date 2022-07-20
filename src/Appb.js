import {useContext,useState} from 'react'
import { BrowserRouter, Routes, Route, Navigate} from 'react-router-dom';
import {UserContext} from './components/UserContext';
import Login from './components/Login';

import Home from './components/Home';
import App from './App';

function Appb() {
const [UsernameAuth, setUsernameAuth] = useState("")
//  const {user} = useContext(UserContext); 
 
  return (
    <div className="container">
        <BrowserRouter>
          <Routes>
            <Route path="/admin" element={<App/>} /> 
            <Route path="/login" element={<Login setUsernameAuth={setUsernameAuth}/>} />
          </Routes>
        </BrowserRouter>
    </div>
  );
}


export default Appb