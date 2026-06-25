
import React, { useState, useEffect } from 'react';
import axios from 'axios';

const ProductsTest = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedCompatibility, setSelectedCompatibility] = useState(null);

  // Configuración de la URL de tu API de Laravel (ajusta el puerto si es necesario)
  const API_URL = 'http://127.0.0.1:8000/api/products';

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await axios.get(API_URL);
      // Laravel usualmente devuelve los datos directos o envueltos en una propiedad 'data'
      const data = response.data.data ? response.data.data : response.data;
      setProducts(data);
      setLoading(false);
    } catch (err) {
      console.error("Error al conectar con la API:", err);
      setError(err.message || "Error al conectar con el servidor backend");
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-screen bg-gray-50">
        <p className="text-xl font-semibold text-blue-600 animate-pulse">Conectando con la API y cargando productos...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex flex-col justify-center items-center h-screen bg-gray-50 p-4">
        <div className="bg-red-100 text-red-700 p-4 rounded-lg max-w-md shadow-md text-center">
          <h3 className="font-bold text-lg mb-2">⚠ Error de Conectividad</h3>
          <p>{error}</p>
          <p className="text-xs mt-2 text-red-500">Asegúrate de que tu backend de Laravel esté corriendo (`php artisan serve`) y que CORS esté habilitado.</p>
          <button onClick={fetchProducts} className="mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
            Reintentar Conexión
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-100 p-6">
      <div className="max-w-7xl mx-auto bg-white rounded-xl shadow-md p-6">
        <div className="flex justify-between items-center mb-6 border-b pb-4">
          <div>
            <h1 className="text-2xl font-bold text-gray-800">Panel de Validación DSS — Tabla Productos</h1>
            <p className="text-sm text-gray-500">Validando conectividad en tiempo real con MariaDB a través de la API</p>
          </div>
          <span className="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full shadow-sm animate-pulse">
            ● API Conectada
          </span>
        </div>

        <div className="overflow-x-auto rounded-lg border border-gray-200">
          <table className="min-w-full divide-y divide-gray-200 text-sm">
            <thead className="bg-gray-50 text-gray-700 uppercase font-semibold text-xs tracking-wider">
              <tr>
                <th className="px-4 py-3 text-left">SKU</th>
                <th className="px-4 py-3 text-left">Nombre del Repuesto</th>
                <th className="px-4 py-3 text-right">Precio Venta</th>
                <th className="px-4 py-3 text-right">Costo</th>
                <th className="px-4 py-3 text-center">Stock</th>
                <th className="px-4 py-3 text-center">Compatibilidad</th>
                <th className="px-4 py-3 text-center">Estado</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200 bg-white text-gray-600">
              {products.length === 0 ? (
                <tr>
                  <td colSpan="7" className="px-4 py-8 text-center text-gray-400">
                    No hay productos disponibles en la base de datos. Ejecuta el seeder.
                  </td>
                </tr>
              ) : (
                products.map((product) => {
                  // Validar e interpretar el campo JSON de compatibilidad
                  let compatibilityObj = null;
                  try {
                    compatibilityObj = typeof product.compatibility === 'string' 
                      ? JSON.parse(product.compatibility) 
                      : product.compatibility;
                  } catch (e) {
                    compatibilityObj = null;
                  }

                  const isLowStock = product.stock <= product.stock_min;

                  return (
                    <tr key={product.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-4 py-3 font-mono font-bold text-blue-600">{product.sku}</td>
                      <td className="px-4 py-3 font-medium text-gray-900">{product.name}</td>
                      <td className="px-4 py-3 text-right font-semibold text-gray-900">${parseFloat(product.price).toFixed(2)}</td>
                      <td className="px-4 py-3 text-right text-gray-500">${parseFloat(product.cost).toFixed(2)}</td>
                      <td className="px-4 py-3 text-center">
                        <span className={`inline-block px-2 py-1 rounded text-xs font-bold ${isLowStock ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-gray-100 text-gray-800'}`}>
                          {product.stock} <span className="text-xs font-normal text-gray-400">/ {product.stock_min}</span>
                        </span>
                      </td>
                      <td className="px-4 py-3 text-center">
                        {compatibilityObj ? (
                          <button 
                            onClick={() => setSelectedCompatibility(compatibilityObj)}
                            className="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded-md text-xs font-medium border border-blue-200 transition"
                          >
                            Ver Datos JSON
                          </button>
                        ) : (
                          <span className="text-gray-400 text-xs">No definida</span>
                        )}
                      </td>
                      <td className="px-4 py-3 text-center">
                        <span className={`px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm ${product.estado === 1 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}`}>
                          {product.estado === 1 ? 'Activo' : 'Inactivo'}
                        </span>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* MODAL SIMPLE PARA MOSTRAR LA COMPATIBILIDAD VEHICULAR (JSON) */}
      {selectedCompatibility && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center p-4 z-50">
          <div className="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 className="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Estructura de Compatibilidad Vehicular</h3>
            <div className="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-xs overflow-y-auto max-h-60 mb-4 shadow-inner">
              <pre>{JSON.stringify(selectedCompatibility, null, 2)}</pre>
            </div>
            <div className="flex justify-end">
              <button 
                onClick={() => setSelectedCompatibility(null)}
                className="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition font-medium text-sm"
              >
                Cerrar Detalle
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ProductsTest;