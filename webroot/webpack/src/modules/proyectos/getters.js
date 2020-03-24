const getProyecto = (state) => (id) => {
    return state.proyectos.find(proyecto => proyecto.id === id)
}

export {
    getProyecto,
}