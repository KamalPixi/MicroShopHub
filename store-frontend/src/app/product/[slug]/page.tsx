import ProductClientPage from "../../../components/ProductClientPage";

export default function Page() {
  return <ProductClientPage />;
}

export async function generateStaticParams() {
  return [{ slug: "placeholder" }];
}
