import OverallStat from "../models/OverallStat.js";
import mongoose from "mongoose";

export const getSales = async (req,res)=>{
    try{

        const overAllStat = await OverallStat.find();

        
        res.status(200).json(overAllStat[0]);
    }catch(error)
    {
        res.status(500).json({"message":error.message});
    }
}