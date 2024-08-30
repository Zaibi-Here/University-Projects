import express  from "express";
import {getAdmin,getUserPerformance} from "../controller/management.js";

const router = express.Router();

router.get("/admin",getAdmin);
router.get("/performance/:id",getUserPerformance);

export default router;