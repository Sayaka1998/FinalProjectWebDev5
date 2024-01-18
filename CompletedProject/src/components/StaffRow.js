function StaffRow(props) {
    const approve = () => { // return the user data to the staff approval
        props.approve(props.staff);
    }

    return(
        <tr>
            <td>{props.staff.uid}</td>
            <td>{props.staff.fname}</td>
            <td>{props.staff.lname}</td>
            <td>{props.staff.email}</td>
            <td>{props.staff.type}</td>
            <td><button type="button" className="btn btn-outline-danger" onClick={approve}>Approve</button>
            </td>
        </tr>
    )
}
export default StaffRow;