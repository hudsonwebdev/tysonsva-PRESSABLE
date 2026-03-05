/**
 * UIkit + Icons loaded in a separate chunk (dynamic import from main.js).
 * Keeps the main entrypoint under webpack's size limit.
 */
import UIkit from "uikit";
import Icons from "uikit/dist/js/uikit-icons";
UIkit.use(Icons);
if (typeof window !== "undefined") {
  window.UIkit = UIkit;
}
