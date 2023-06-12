 import React, {
    Component
} from 'react';
 
  


import Modal from 'react-modal';
const customStyles = {
    content : {
      top                   : '50%',
      left                  : '50%',
      width                 : '30%',
 
      transform             : 'translate(-50%, -50%)'
    }
  };
 class ConfirmationModal extends Component {

    constructor(props) {
        super(props);
        this.state = {
  
            modalIsOpen: false,
            objectoCtb:[],
           
            objectoCtbRow:[],
            idContri:''
        };
        this.openModal = this.openModal.bind(this);
         this.closeModal = this.closeModal.bind(this);
         this.afterOpenModal = this.afterOpenModal.bind(this);
        //  this.eventClickTable = this.eventClickTable.bind(this);
        //  this.getDataCtbSelected = this.getDataCtbSelected.bind(this);
        //  this.loadStorage = this.loadStorage.bind(this);

    }
    openModal() {
        this.setState({modalIsOpen: true});
    }
    
    closeModal() {
        this.setState({modalIsOpen: false}); 
        //  sessionStorage.removeItem("data");
    }
    afterOpenModal(){
        // var t = document.getElementById('clickEvent');
        // t.onclick =  this.eventClickTable;
    }

    componentDidMount() { // https://reactjs.org/docs/react-component.html#componentdidmount
 
         if (typeof(window) !== 'undefined') {
            Modal.setAppElement('body');
          }

 
      
    }

 
 
    render() {
       

        return (
        <div className="container-okc">

        <Modal
          isOpen={this.state.modalIsOpen}
          onAfterOpen={this.afterOpenModal}
           onRequestClose={this.closeModal}
        //   ariaHideApp={false}
          style={customStyles}
          >
          
       
             <div className="modal-okc-header">
                <button type="button" className="close" onClick={this.closeModal}><span>x</span></button>
                <h5>Confirmar Acción</h5>
            </div>

          <div className="container-fluid">
                <div className="modulo" id="doc_identidad">
                <br/>
                    <div className="row">
                        <div className="col-12">
                            <div className="row justify-content-md-center">
                                <div className="col-sm-12 text-center"> 
                                    
                                <button className="btn btn-primary btn-lg" onClick={(e)=>{e.preventDefault(); this.loadStorage()}}>Confirmar</button>
                                    &nbsp;<button className="btn btn-danger btn-lg" onClick={this.closeModal}>Cancelar</button>
                            </div>
                            </div> 
                        </div>
                    </div>
                <br/>
                </div>
            </div>
          <form>
          </form>
        </Modal>
 
        </div>
        )
    }
}

ConfirmationModal.propType={
    // loadReady: PropType.func
   }


export default ConfirmationModal;