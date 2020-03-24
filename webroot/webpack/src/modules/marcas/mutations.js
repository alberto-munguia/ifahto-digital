const setMarca = (state, marca) => {
    state.marca = marca
}

const setMarcas = (state, marcas) => {
    state.marcas = marcas
}

const isSaved = (state, isSaved) => {
    state.marcaGuardada = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.marcaEliminada = isDeleted
}

export {
    setMarca,
    setMarcas,
    isSaved,
    isDeleted,
}