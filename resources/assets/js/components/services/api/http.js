import axios from 'axios';

 // const baseURL = 'http://localhost:8000/api/'
// const baseURL = 'http://192.168.20.2:8080/api/'
const baseURL = 'http://127.0.0.1:8000/api/'


export default axios.create({
  baseURL ,
  headers: {'Content-Type': 'application/json'}


});



