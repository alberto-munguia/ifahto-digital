const setProyecto = (state, proyecto) => {
    state.proyecto = proyecto
}

const setProyectos = (state, proyectos) => {
    state.proyectos = proyectos
}

const isSaved = (state, isSaved) => {
    state.proyectoGuardado = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.proyectoEliminado = isDeleted
}

const setPeriodicidadPagos = (state, periodicidades) => {
    state.periodicidadPagos = periodicidades
}

const setTipoServicios = (state, tipoServicios) => {
    state.tipoServicios = tipoServicios
}

export {
    setProyecto,
    setProyectos,
    isSaved,
    isDeleted,
    setPeriodicidadPagos,
    setTipoServicios,
}