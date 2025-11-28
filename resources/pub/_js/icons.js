import {
  createIcons,
  Barcode,
  Book,
  BookOpen,
  BookOpenText,
  BrushCleaning,
  Building,
  Calendar,
  CaseSensitive,
  CheckCircle,
  Drama,
  Flag,
  Languages,
  LogOut,
  MessageCircle,
  MessageCircleOff,
  Package,
  PackageOpen,
  PackageX,
  Search,
  Signature,
  Star,
  TableProperties,
  User,
} from "lucide";

export const iconRegistry = {
  Barcode,
  Book,
  BookOpen,
  BookOpenText,
  BrushCleaning,
  Building,
  Calendar,
  CaseSensitive,
  CheckCircle,
  Drama,
  Flag,
  Languages,
  LogOut,
  MessageCircle,
  MessageCircleOff,
  Package,
  PackageOpen,
  PackageX,
  Search,
  Signature,
  Star,
  TableProperties,
  User,
};

export function renderIcons() {
  createIcons({ icons: iconRegistry });
}

document.addEventListener("DOMContentLoaded", renderIcons());
