import mongoose from "mongoose";

const productSchema = new mongoose.Schema({
  name: String,
  price: Number,
  description: String,
  category: String,
  rating: Number,
  supply: Number,
},{timestamps:true});

const Product = new mongoose.model("Product", productSchema);

export default Product;
