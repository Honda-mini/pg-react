import { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";

export default function ContactPage() {
  const location = useLocation();
  const navigate = useNavigate();

  const params = new URLSearchParams(location.search);
  const carName = params.get("car") || "";
  const carID = params.get("id") || "";

  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState("");

  // Prefill message if coming from a vehicle page
  useEffect(() => {
    if (carName || carID) {
      setMessage(
        `Hi,\n\nI'm interested in reserving the ${carName} (Stock ID: ${carID}).\n\nPlease let me know the next steps.\n\nThanks!`
      );
    }
  }, [carName, carID]);

  const handleSubmit = (e) => {
    e.preventDefault();

    const mailto = `mailto:info@pgservices.net?subject=Enquiry%20from%20${encodeURIComponent(
      name
    )}&body=${encodeURIComponent(message + "\n\nReply to: " + email)}`;

    window.location.href = mailto;

    // After sending, go back to previous page
    setTimeout(() => navigate(-1), 500);
  };

  return (
    <section className="py-20 bg-gray-50 dark:bg-gray-800 min-h-screen">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 max-w-2xl">
        <h1 className="text-4xl font-bold text-gray-900 dark:text-white mb-6">
          Contact Us
        </h1>

        <p className="text-gray-600 dark:text-gray-300 mb-10">
          Have a question or want to reserve a vehicle? Send us a message and
          we’ll get back to you shortly.
        </p>

        <form
          onSubmit={handleSubmit}
          className="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-lg space-y-6"
        >
          <div>
            <label className="block text-gray-700 dark:text-gray-300 mb-2">
              Your Name
            </label>
            <input
              type="text"
              required
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="w-full p-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white"
            />
          </div>

          <div>
            <label className="block text-gray-700 dark:text-gray-300 mb-2">
              Email Address
            </label>
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full p-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white"
            />
          </div>

          <div>
            <label className="block text-gray-700 dark:text-gray-300 mb-2">
              Message
            </label>
            <textarea
              required
              rows={6}
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              className="w-full p-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white"
            />
          </div>

          <button
            type="submit"
            className="w-full py-3 rounded-lg bg-blue-600 dark:bg-yellow-500 text-white dark:text-gray-900 hover:bg-blue-700 dark:hover:bg-yellow-400 transition-colors"
          >
            Send Message
          </button>
        </form>
      </div>
    </section>
  );
}
