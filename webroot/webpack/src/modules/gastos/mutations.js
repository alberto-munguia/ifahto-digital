const setGasto = (state, gasto) => {
    state.gasto = gasto
}

const setGastos = (state, gastos) => {
    state.gastos = gastos
}

const setTipoGastos = (state, tipoGastos) => {
    state.tipoGastos = tipoGastos
}

const setTipoPagos = (state, tipoPagos) => {
    state.tipoPagos = tipoPagos
}

const isSaved = (state, isSaved) => {
    state.gastoGuardado = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.gastoEliminado = isDeleted
}

export {
    setGasto,
    setGastos,
    setTipoGastos,
    setTipoPagos,
    isSaved,
    isDeleted,
}