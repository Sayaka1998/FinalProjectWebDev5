import './css/App.css';
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Menu from './components/Menu';
import Login from './components/Login';
import Register from "./components/Register";
import Blist from './components/BookList';
import Nopage from './components/Nopage';
import StaffApproval from './components/StaffApproval';
import BookReg from './components/BookReg';

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path='/' element={<Menu />}>
                    <Route path='/' element={<Login />} />
                    <Route path='register' element={<Register />} />
                    <Route path='stAppr' element={<StaffApproval />} />
                    <Route path='blist' element={<Blist />} />
                    <Route path='breg' element={<BookReg />} />
                    <Route path="*" element={<Nopage />} />
                </Route>
            </Routes>
        </BrowserRouter>
    )
}

export default App;
