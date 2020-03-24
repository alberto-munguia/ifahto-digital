const setFacturacion = (state, facturacion) => {
    state.facturacion = facturacion
}

const setFacturaciones = (state, facturaciones) => {
    state.facturaciones = facturaciones
}

const setTipoFacturaciones = (state, tipoFacturaciones) => {
    state.tipoFacturaciones = tipoFacturaciones
}

const isSaved = (state, isSaved) => {
    state.facturacionGuardado = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.facturacionEliminado = isDeleted
}

export {
    setFacturacion,
    setFacturaciones,
    setTipoFacturaciones,
    isSaved,
    isDeleted,
}