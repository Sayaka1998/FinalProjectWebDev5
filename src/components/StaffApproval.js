import { useNavigate } from "react-router-dom";
import { useState, useEffect} from "react";
import httpSrv from "../services/httpSrv";
import StaffRow from "./StaffRow";
function StaffApproval() {
    const nav = useNavigate();
    useEffect(()=>{
        if(sessionStorage.getItem("sid") == undefined) {
            nav("/");
        }
    });

    const appr = (staffData) => {
        let data = new FormData();
        data.append("approve",JSON.stringify(staffData));
        data.append("sid",sessionStorage.getItem("sid"));
        httpSrv.approve(data).then(
            res => {
               alert(res.data);
            },
            rej => {
                alert(rej);
            }
        )
    } 

    const [staffs, setStaffs] = useState([]);

    const loadAlist = () => {
        if (staffs.length === 0) {
            let data = new FormData();
            data.append("sid",sessionStorage.getItem("sid"));
            httpSrv.alist(data).then(
                res => {
                    if(Array.isArray(res.data)) {
                        setStaffs(res.data);
                    }
                },
                rej => {
                    alert(rej);
                }
            )
        }
    }
    loadAlist();

    return(
        <div className="container-fluid">
            <div className="row justify-content-center align-items-center g-2">
                <div className="col">
                    <div className="table-responsive">
                        <table className="table table-primary">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                {staffs.length != 0 ? (staffs.map((staff, idx) => (<StaffRow key={idx} staff={staff} approve={appr} />))) : (<tr><td colSpan="6">No staff awaiting</td></tr>)}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    )
}
export default StaffApproval;