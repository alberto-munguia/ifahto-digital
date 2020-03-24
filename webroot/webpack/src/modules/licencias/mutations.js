const setLicencia = (state, licencia) => {
    state.licencia = licencia
}

const setLicencias = (state, licencias) => {
    state.licencias = licencias
}

const isSaved = (state, isSaved) => {
    state.licenciaGuardada = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.licenciaEliminada = isDeleted
}

export {
    setLicencia,
    setLicencias,
    isSaved,
    isDeleted,
}