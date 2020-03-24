const getUsuariosByPuesto = (state) => (puesto) => {
    return state.usuarios.filter(usuario => usuario.puesto === puesto)
}

export {
    getUsuariosByPuesto,
}