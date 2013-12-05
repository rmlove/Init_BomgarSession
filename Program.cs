using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Net;
using System.Xml;
using System.IO;
using System.Xml.Linq;

namespace ConsoleApplication1
{
    class Program
    {
        static void Main(string[] args)
        {
            //bomgar appliance information
            string hostname = "example.bomgar.com";
            string apiUsername = "api";
            string apiPassword = "password";

            
            string incidentId = "INC1000234";
            string representativeBomgarUsername = "Admin"; //Bomgar Rep's Username

            String sessionKey = GenerateSessionKey(hostname, apiUsername, apiPassword, incidentId, representativeBomgarUsername);

            Console.WriteLine(sessionKey);
            Console.ReadKey();
        }

        static string GenerateSessionKey(string hostname, string apiUsername, string apiPassword, string incidentId, string representativeBomgarUsername)
        {
            string sessionKey = null;
            //construct the API call URL
            string url = "https://" + hostname + "/api/command.ns?action=generate_session_key&type=support&queue_id=rep_username:" + representativeBomgarUsername + "&external_key=" + incidentId + "&username=" + apiUsername + "&password=" + apiPassword;
            Console.WriteLine("Generate Session Key API URL: " + url);
            WebClient webClient = new WebClient();
            string xml = webClient.DownloadString(url);
            sessionKey = XDocument.Parse(xml).Descendants("short_key").First<XElement>().Value;
            return sessionKey;
        }
    }
}
