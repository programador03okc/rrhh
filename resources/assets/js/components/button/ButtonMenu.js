import React, { Component} from "react";
import PropType from 'prop-types';

class ButtonMenu extends Component {
constructor(props){
    super(props);
    this.state={
        buttonNuevoDisabled:false,
        buttonGuardarDisabled:true,
        buttonEditarDisabled:false,
        buttonAnularDisabled:false,
        buttonHistorialDisabled:false,
        buttonCancelarDisabled:true
    }
}
    
 handleClick(target,e){
     e.preventDefault();
    switch(target){
        case 'nuevo':     
        this.getEventClickMenu('nuevo');
        this.setState({buttonGuardarDisabled:false});
        this.setState({buttonEditarDisabled:true});
        this.setState({buttonAnularDisabled:true});
        this.setState({buttonHistorialDisabled:true});
        this.setState({buttonCancelarDisabled:false});
        this.setState({buttonNuevoDisabled:true});
        break;
        case 'guardar':
        this.getEventClickMenu('guardar');
        this.setState({buttonNuevoDisabled:false});
        this.setState({buttonGuardarDisabled:true});
        this.setState({buttonEditarDisabled:false});
        this.setState({buttonAnularDisabled:false});
        this.setState({buttonHistorialDisabled:false});
        this.setState({buttonCancelarDisabled:true});
        break;
        case 'editar':
        this.getEventClickMenu('editar');
        this.setState({buttonNuevoDisabled:true});
        this.setState({buttonGuardarDisabled:false});
        this.setState({buttonEditarDisabled:true});
        this.setState({buttonAnularDisabled:true});
        this.setState({buttonHistorialDisabled:true});
        this.setState({buttonCancelarDisabled:false});
        break;
        case 'anular':
        this.getEventClickMenu('anular');
        break;
        case 'historial':
        this.getEventClickMenu('historial');
        break;
        case 'cancelar':
        this.getEventClickMenu('cancelar');
        this.setState({buttonNuevoDisabled:false});
        this.setState({buttonGuardarDisabled:true});
        this.setState({buttonEditarDisabled:false});
        this.setState({buttonAnularDisabled:false});
        this.setState({buttonHistorialDisabled:false});
        this.setState({buttonCancelarDisabled:true});
        break;
        default:
    }
}

getEventClickMenu(e){
      this.props.eventClick(e);

}


render(){
 return(
        <div className="base-options">
        <button className="btn-okc-menu" id="btn-new"  onClick={ this.handleClick.bind(this,"nuevo")} disabled={this.state.buttonNuevoDisabled}>
        <i className="fas fa-file fa-lg"></i><br/>
            Nuevo
        </button>
        <button className="btn-okc-menu" id="btn-save" onClick={ this.handleClick.bind(this,"guardar")} disabled={this.state.buttonGuardarDisabled}>
        <i className="fas fa-save fa-lg"></i><br/>
            Guardar
        </button>
        <button className="btn-okc-menu" id="btn-edit" onClick={ this.handleClick.bind(this,"editar")} disabled={this.state.buttonEditarDisabled}>
        <i className="fas fa-edit fa-lg"></i><br/>
            Editar
        </button>
        <button className="btn-okc-menu" id="btn-delete" onClick={ this.handleClick.bind(this,"anular")} disabled={this.state.buttonAnularDisabled}>
        <i className="fas fa-trash fa-lg"></i><br/>
            Anular
        </button>
        <button className="btn-okc-menu" id="btn-historial" onClick={ this.handleClick.bind(this,"historial")} disabled={this.state.buttonHistorialDisabled}>
        <i className="fas fa-folder fa-lg"></i><br/>
            Historial
        </button>
        <button className="btn-okc-menu" id="btn-cancel" onClick={ this.handleClick.bind(this,"cancelar")} disabled={this.state.buttonCancelarDisabled}>
        <i className="fas fa-times fa-lg"></i><br/>
            Cancelar
        </button>
         
        </div>
);
}
}
ButtonMenu.propType={
    eventClick:PropType.func
 }


export default ButtonMenu;
