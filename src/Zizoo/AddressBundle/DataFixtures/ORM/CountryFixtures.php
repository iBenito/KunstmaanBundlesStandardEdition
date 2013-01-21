<?php

namespace Zizoo\AddressBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\AddressBundle\Entity\Country;

class CountryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $countryAD = new Country();
        $countryAD->setIso("AD");
        $countryAD->setIso3("AND");
        $countryAD->setName("ANDORRA");
        $countryAD->setNumcode("20");
        $countryAD->setPrintableName("Andorra");
        $manager->persist($countryAD);

        $countryAE = new Country();
        $countryAE->setIso("AE");
        $countryAE->setIso3("ARE");
        $countryAE->setName("UNITED ARAB EMIRATES");
        $countryAE->setNumcode("784");
        $countryAE->setPrintableName("United Arab Emirates");
        $manager->persist($countryAE);

        $countryAF = new Country();
        $countryAF->setIso("AF");
        $countryAF->setIso3("AFG");
        $countryAF->setName("AFGHANISTAN");
        $countryAF->setNumcode("4");
        $countryAF->setPrintableName("Afghanistan");
        $manager->persist($countryAF);

        $countryAG = new Country();
        $countryAG->setIso("AG");
        $countryAG->setIso3("ATG");
        $countryAG->setName("ANTIGUA AND BARBUDA");
        $countryAG->setNumcode("28");
        $countryAG->setPrintableName("Antigua and Barbuda");
        $manager->persist($countryAG);

        $countryAI = new Country();
        $countryAI->setIso("AI");
        $countryAI->setIso3("AIA");
        $countryAI->setName("ANGUILLA");
        $countryAI->setNumcode("660");
        $countryAI->setPrintableName("Anguilla");
        $manager->persist($countryAI);

        $countryAL = new Country();
        $countryAL->setIso("AL");
        $countryAL->setIso3("ALB");
        $countryAL->setName("ALBANIA");
        $countryAL->setNumcode("8");
        $countryAL->setPrintableName("Albania");
        $manager->persist($countryAL);

        $countryAM = new Country();
        $countryAM->setIso("AM");
        $countryAM->setIso3("ARM");
        $countryAM->setName("ARMENIA");
        $countryAM->setNumcode("51");
        $countryAM->setPrintableName("Armenia");
        $manager->persist($countryAM);

        $countryAN = new Country();
        $countryAN->setIso("AN");
        $countryAN->setIso3("ANT");
        $countryAN->setName("NETHERLANDS ANTILLES");
        $countryAN->setNumcode("530");
        $countryAN->setPrintableName("Netherlands Antilles");
        $manager->persist($countryAN);

        $countryAO = new Country();
        $countryAO->setIso("AO");
        $countryAO->setIso3("AGO");
        $countryAO->setName("ANGOLA");
        $countryAO->setNumcode("24");
        $countryAO->setPrintableName("Angola");
        $manager->persist($countryAO);

        $countryAQ = new Country();
        $countryAQ->setIso("AQ");
        $countryAQ->setName("ANTARCTICA");
        $countryAQ->setPrintableName("Antarctica");
        $manager->persist($countryAQ);

        $countryAR = new Country();
        $countryAR->setIso("AR");
        $countryAR->setIso3("ARG");
        $countryAR->setName("ARGENTINA");
        $countryAR->setNumcode("32");
        $countryAR->setPrintableName("Argentina");
        $manager->persist($countryAR);

        $countryAS = new Country();
        $countryAS->setIso("AS");
        $countryAS->setIso3("ASM");
        $countryAS->setName("AMERICAN SAMOA");
        $countryAS->setNumcode("16");
        $countryAS->setPrintableName("American Samoa");
        $manager->persist($countryAS);

        $countryAT = new Country();
        $countryAT->setIso("AT");
        $countryAT->setIso3("AUT");
        $countryAT->setName("AUSTRIA");
        $countryAT->setNumcode("40");
        $countryAT->setPrintableName("Austria");
        $manager->persist($countryAT);

        $countryAU = new Country();
        $countryAU->setIso("AU");
        $countryAU->setIso3("AUS");
        $countryAU->setName("AUSTRALIA");
        $countryAU->setNumcode("36");
        $countryAU->setPrintableName("Australia");
        $manager->persist($countryAU);

        $countryAW = new Country();
        $countryAW->setIso("AW");
        $countryAW->setIso3("ABW");
        $countryAW->setName("ARUBA");
        $countryAW->setNumcode("533");
        $countryAW->setPrintableName("Aruba");
        $manager->persist($countryAW);

        $countryAZ = new Country();
        $countryAZ->setIso("AZ");
        $countryAZ->setIso3("AZE");
        $countryAZ->setName("AZERBAIJAN");
        $countryAZ->setNumcode("31");
        $countryAZ->setPrintableName("Azerbaijan");
        $manager->persist($countryAZ);

        $countryBA = new Country();
        $countryBA->setIso("BA");
        $countryBA->setIso3("BIH");
        $countryBA->setName("BOSNIA AND HERZEGOVINA");
        $countryBA->setNumcode("70");
        $countryBA->setPrintableName("Bosnia and Herzegovina");
        $manager->persist($countryBA);

        $countryBB = new Country();
        $countryBB->setIso("BB");
        $countryBB->setIso3("BRB");
        $countryBB->setName("BARBADOS");
        $countryBB->setNumcode("52");
        $countryBB->setPrintableName("Barbados");
        $manager->persist($countryBB);

        $countryBD = new Country();
        $countryBD->setIso("BD");
        $countryBD->setIso3("BGD");
        $countryBD->setName("BANGLADESH");
        $countryBD->setNumcode("50");
        $countryBD->setPrintableName("Bangladesh");
        $manager->persist($countryBD);

        $countryBE = new Country();
        $countryBE->setIso("BE");
        $countryBE->setIso3("BEL");
        $countryBE->setName("BELGIUM");
        $countryBE->setNumcode("56");
        $countryBE->setPrintableName("Belgium");
        $manager->persist($countryBE);

        $countryBF = new Country();
        $countryBF->setIso("BF");
        $countryBF->setIso3("BFA");
        $countryBF->setName("BURKINA FASO");
        $countryBF->setNumcode("854");
        $countryBF->setPrintableName("Burkina Faso");
        $manager->persist($countryBF);

        $countryBG = new Country();
        $countryBG->setIso("BG");
        $countryBG->setIso3("BGR");
        $countryBG->setName("BULGARIA");
        $countryBG->setNumcode("100");
        $countryBG->setPrintableName("Bulgaria");
        $manager->persist($countryBG);

        $countryBH = new Country();
        $countryBH->setIso("BH");
        $countryBH->setIso3("BHR");
        $countryBH->setName("BAHRAIN");
        $countryBH->setNumcode("48");
        $countryBH->setPrintableName("Bahrain");
        $manager->persist($countryBH);

        $countryBI = new Country();
        $countryBI->setIso("BI");
        $countryBI->setIso3("BDI");
        $countryBI->setName("BURUNDI");
        $countryBI->setNumcode("108");
        $countryBI->setPrintableName("Burundi");
        $manager->persist($countryBI);

        $countryBJ = new Country();
        $countryBJ->setIso("BJ");
        $countryBJ->setIso3("BEN");
        $countryBJ->setName("BENIN");
        $countryBJ->setNumcode("204");
        $countryBJ->setPrintableName("Benin");
        $manager->persist($countryBJ);

        $countryBM = new Country();
        $countryBM->setIso("BM");
        $countryBM->setIso3("BMU");
        $countryBM->setName("BERMUDA");
        $countryBM->setNumcode("60");
        $countryBM->setPrintableName("Bermuda");
        $manager->persist($countryBM);

        $countryBN = new Country();
        $countryBN->setIso("BN");
        $countryBN->setIso3("BRN");
        $countryBN->setName("BRUNEI DARUSSALAM");
        $countryBN->setNumcode("96");
        $countryBN->setPrintableName("Brunei Darussalam");
        $manager->persist($countryBN);

        $countryBO = new Country();
        $countryBO->setIso("BO");
        $countryBO->setIso3("BOL");
        $countryBO->setName("BOLIVIA");
        $countryBO->setNumcode("68");
        $countryBO->setPrintableName("Bolivia");
        $manager->persist($countryBO);

        $countryBR = new Country();
        $countryBR->setIso("BR");
        $countryBR->setIso3("BRA");
        $countryBR->setName("BRAZIL");
        $countryBR->setNumcode("76");
        $countryBR->setPrintableName("Brazil");
        $manager->persist($countryBR);

        $countryBS = new Country();
        $countryBS->setIso("BS");
        $countryBS->setIso3("BHS");
        $countryBS->setName("BAHAMAS");
        $countryBS->setNumcode("44");
        $countryBS->setPrintableName("Bahamas");
        $manager->persist($countryBS);

        $countryBT = new Country();
        $countryBT->setIso("BT");
        $countryBT->setIso3("BTN");
        $countryBT->setName("BHUTAN");
        $countryBT->setNumcode("64");
        $countryBT->setPrintableName("Bhutan");
        $manager->persist($countryBT);

        $countryBV = new Country();
        $countryBV->setIso("BV");
        $countryBV->setName("BOUVET ISLAND");
        $countryBV->setPrintableName("Bouvet Island");
        $manager->persist($countryBV);

        $countryBW = new Country();
        $countryBW->setIso("BW");
        $countryBW->setIso3("BWA");
        $countryBW->setName("BOTSWANA");
        $countryBW->setNumcode("72");
        $countryBW->setPrintableName("Botswana");
        $manager->persist($countryBW);

        $countryBY = new Country();
        $countryBY->setIso("BY");
        $countryBY->setIso3("BLR");
        $countryBY->setName("BELARUS");
        $countryBY->setNumcode("112");
        $countryBY->setPrintableName("Belarus");
        $manager->persist($countryBY);

        $countryBZ = new Country();
        $countryBZ->setIso("BZ");
        $countryBZ->setIso3("BLZ");
        $countryBZ->setName("BELIZE");
        $countryBZ->setNumcode("84");
        $countryBZ->setPrintableName("Belize");
        $manager->persist($countryBZ);

        $countryCA = new Country();
        $countryCA->setIso("CA");
        $countryCA->setIso3("CAN");
        $countryCA->setName("CANADA");
        $countryCA->setNumcode("124");
        $countryCA->setPrintableName("Canada");
        $manager->persist($countryCA);

        $countryCC = new Country();
        $countryCC->setIso("CC");
        $countryCC->setName("COCOS (KEELING) ISLANDS");
        $countryCC->setPrintableName("Cocos (Keeling) Islands");
        $manager->persist($countryCC);

        $countryCD = new Country();
        $countryCD->setIso("CD");
        $countryCD->setIso3("COD");
        $countryCD->setName("CONGO, THE DEMOCRATIC REPUBLIC OF THE");
        $countryCD->setNumcode("180");
        $countryCD->setPrintableName("Congo, the Democratic Republic of the");
        $manager->persist($countryCD);

        $countryCF = new Country();
        $countryCF->setIso("CF");
        $countryCF->setIso3("CAF");
        $countryCF->setName("CENTRAL AFRICAN REPUBLIC");
        $countryCF->setNumcode("140");
        $countryCF->setPrintableName("Central African Republic");
        $manager->persist($countryCF);

        $countryCG = new Country();
        $countryCG->setIso("CG");
        $countryCG->setIso3("COG");
        $countryCG->setName("CONGO");
        $countryCG->setNumcode("178");
        $countryCG->setPrintableName("Congo");
        $manager->persist($countryCG);

        $countryCH = new Country();
        $countryCH->setIso("CH");
        $countryCH->setIso3("CHE");
        $countryCH->setName("SWITZERLAND");
        $countryCH->setNumcode("756");
        $countryCH->setPrintableName("Switzerland");
        $manager->persist($countryCH);

        $countryCI = new Country();
        $countryCI->setIso("CI");
        $countryCI->setIso3("CIV");
        $countryCI->setName("COTE D'IVOIRE");
        $countryCI->setNumcode("384");
        $countryCI->setPrintableName("Cote D'Ivoire");
        $manager->persist($countryCI);

        $countryCK = new Country();
        $countryCK->setIso("CK");
        $countryCK->setIso3("COK");
        $countryCK->setName("COOK ISLANDS");
        $countryCK->setNumcode("184");
        $countryCK->setPrintableName("Cook Islands");
        $manager->persist($countryCK);

        $countryCL = new Country();
        $countryCL->setIso("CL");
        $countryCL->setIso3("CHL");
        $countryCL->setName("CHILE");
        $countryCL->setNumcode("152");
        $countryCL->setPrintableName("Chile");
        $manager->persist($countryCL);

        $countryCM = new Country();
        $countryCM->setIso("CM");
        $countryCM->setIso3("CMR");
        $countryCM->setName("CAMEROON");
        $countryCM->setNumcode("120");
        $countryCM->setPrintableName("Cameroon");
        $manager->persist($countryCM);

        $countryCN = new Country();
        $countryCN->setIso("CN");
        $countryCN->setIso3("CHN");
        $countryCN->setName("CHINA");
        $countryCN->setNumcode("156");
        $countryCN->setPrintableName("China");
        $manager->persist($countryCN);

        $countryCO = new Country();
        $countryCO->setIso("CO");
        $countryCO->setIso3("COL");
        $countryCO->setName("COLOMBIA");
        $countryCO->setNumcode("170");
        $countryCO->setPrintableName("Colombia");
        $manager->persist($countryCO);

        $countryCR = new Country();
        $countryCR->setIso("CR");
        $countryCR->setIso3("CRI");
        $countryCR->setName("COSTA RICA");
        $countryCR->setNumcode("188");
        $countryCR->setPrintableName("Costa Rica");
        $manager->persist($countryCR);

        $countryCS = new Country();
        $countryCS->setIso("CS");
        $countryCS->setName("SERBIA AND MONTENEGRO");
        $countryCS->setPrintableName("Serbia and Montenegro");
        $manager->persist($countryCS);

        $countryCU = new Country();
        $countryCU->setIso("CU");
        $countryCU->setIso3("CUB");
        $countryCU->setName("CUBA");
        $countryCU->setNumcode("192");
        $countryCU->setPrintableName("Cuba");
        $manager->persist($countryCU);

        $countryCV = new Country();
        $countryCV->setIso("CV");
        $countryCV->setIso3("CPV");
        $countryCV->setName("CAPE VERDE");
        $countryCV->setNumcode("132");
        $countryCV->setPrintableName("Cape Verde");
        $manager->persist($countryCV);

        $countryCX = new Country();
        $countryCX->setIso("CX");
        $countryCX->setName("CHRISTMAS ISLAND");
        $countryCX->setPrintableName("Christmas Island");
        $manager->persist($countryCX);

        $countryCY = new Country();
        $countryCY->setIso("CY");
        $countryCY->setIso3("CYP");
        $countryCY->setName("CYPRUS");
        $countryCY->setNumcode("196");
        $countryCY->setPrintableName("Cyprus");
        $manager->persist($countryCY);

        $countryCZ = new Country();
        $countryCZ->setIso("CZ");
        $countryCZ->setIso3("CZE");
        $countryCZ->setName("CZECH REPUBLIC");
        $countryCZ->setNumcode("203");
        $countryCZ->setPrintableName("Czech Republic");
        $manager->persist($countryCZ);

        $countryDE = new Country();
        $countryDE->setIso("DE");
        $countryDE->setIso3("DEU");
        $countryDE->setName("GERMANY");
        $countryDE->setNumcode("276");
        $countryDE->setPrintableName("Germany");
        $manager->persist($countryDE);

        $countryDJ = new Country();
        $countryDJ->setIso("DJ");
        $countryDJ->setIso3("DJI");
        $countryDJ->setName("DJIBOUTI");
        $countryDJ->setNumcode("262");
        $countryDJ->setPrintableName("Djibouti");
        $manager->persist($countryDJ);

        $countryDK = new Country();
        $countryDK->setIso("DK");
        $countryDK->setIso3("DNK");
        $countryDK->setName("DENMARK");
        $countryDK->setNumcode("208");
        $countryDK->setPrintableName("Denmark");
        $manager->persist($countryDK);

        $countryDM = new Country();
        $countryDM->setIso("DM");
        $countryDM->setIso3("DMA");
        $countryDM->setName("DOMINICA");
        $countryDM->setNumcode("212");
        $countryDM->setPrintableName("Dominica");
        $manager->persist($countryDM);

        $countryDO = new Country();
        $countryDO->setIso("DO");
        $countryDO->setIso3("DOM");
        $countryDO->setName("DOMINICAN REPUBLIC");
        $countryDO->setNumcode("214");
        $countryDO->setPrintableName("Dominican Republic");
        $manager->persist($countryDO);

        $countryDZ = new Country();
        $countryDZ->setIso("DZ");
        $countryDZ->setIso3("DZA");
        $countryDZ->setName("ALGERIA");
        $countryDZ->setNumcode("12");
        $countryDZ->setPrintableName("Algeria");
        $manager->persist($countryDZ);

        $countryEC = new Country();
        $countryEC->setIso("EC");
        $countryEC->setIso3("ECU");
        $countryEC->setName("ECUADOR");
        $countryEC->setNumcode("218");
        $countryEC->setPrintableName("Ecuador");
        $manager->persist($countryEC);

        $countryEE = new Country();
        $countryEE->setIso("EE");
        $countryEE->setIso3("EST");
        $countryEE->setName("ESTONIA");
        $countryEE->setNumcode("233");
        $countryEE->setPrintableName("Estonia");
        $manager->persist($countryEE);

        $countryEG = new Country();
        $countryEG->setIso("EG");
        $countryEG->setIso3("EGY");
        $countryEG->setName("EGYPT");
        $countryEG->setNumcode("818");
        $countryEG->setPrintableName("Egypt");
        $manager->persist($countryEG);

        $countryEH = new Country();
        $countryEH->setIso("EH");
        $countryEH->setIso3("ESH");
        $countryEH->setName("WESTERN SAHARA");
        $countryEH->setNumcode("732");
        $countryEH->setPrintableName("Western Sahara");
        $manager->persist($countryEH);

        $countryER = new Country();
        $countryER->setIso("ER");
        $countryER->setIso3("ERI");
        $countryER->setName("ERITREA");
        $countryER->setNumcode("232");
        $countryER->setPrintableName("Eritrea");
        $manager->persist($countryER);

        $countryES = new Country();
        $countryES->setIso("ES");
        $countryES->setIso3("ESP");
        $countryES->setName("SPAIN");
        $countryES->setNumcode("724");
        $countryES->setPrintableName("Spain");
        $manager->persist($countryES);

        $countryET = new Country();
        $countryET->setIso("ET");
        $countryET->setIso3("ETH");
        $countryET->setName("ETHIOPIA");
        $countryET->setNumcode("231");
        $countryET->setPrintableName("Ethiopia");
        $manager->persist($countryET);

        $countryFI = new Country();
        $countryFI->setIso("FI");
        $countryFI->setIso3("FIN");
        $countryFI->setName("FINLAND");
        $countryFI->setNumcode("246");
        $countryFI->setPrintableName("Finland");
        $manager->persist($countryFI);

        $countryFJ = new Country();
        $countryFJ->setIso("FJ");
        $countryFJ->setIso3("FJI");
        $countryFJ->setName("FIJI");
        $countryFJ->setNumcode("242");
        $countryFJ->setPrintableName("Fiji");
        $manager->persist($countryFJ);

        $countryFK = new Country();
        $countryFK->setIso("FK");
        $countryFK->setIso3("FLK");
        $countryFK->setName("FALKLAND ISLANDS (MALVINAS)");
        $countryFK->setNumcode("238");
        $countryFK->setPrintableName("Falkland Islands (Malvinas)");
        $manager->persist($countryFK);

        $countryFM = new Country();
        $countryFM->setIso("FM");
        $countryFM->setIso3("FSM");
        $countryFM->setName("MICRONESIA, FEDERATED STATES OF");
        $countryFM->setNumcode("583");
        $countryFM->setPrintableName("Micronesia, Federated States of");
        $manager->persist($countryFM);

        $countryFO = new Country();
        $countryFO->setIso("FO");
        $countryFO->setIso3("FRO");
        $countryFO->setName("FAROE ISLANDS");
        $countryFO->setNumcode("234");
        $countryFO->setPrintableName("Faroe Islands");
        $manager->persist($countryFO);

        $countryFR = new Country();
        $countryFR->setIso("FR");
        $countryFR->setIso3("FRA");
        $countryFR->setName("FRANCE");
        $countryFR->setNumcode("250");
        $countryFR->setPrintableName("France");
        $manager->persist($countryFR);

        $countryGA = new Country();
        $countryGA->setIso("GA");
        $countryGA->setIso3("GAB");
        $countryGA->setName("GABON");
        $countryGA->setNumcode("266");
        $countryGA->setPrintableName("Gabon");
        $manager->persist($countryGA);

        $countryGB = new Country();
        $countryGB->setIso("GB");
        $countryGB->setIso3("GBR");
        $countryGB->setName("UNITED KINGDOM");
        $countryGB->setNumcode("826");
        $countryGB->setPrintableName("United Kingdom");
        $manager->persist($countryGB);

        $countryGD = new Country();
        $countryGD->setIso("GD");
        $countryGD->setIso3("GRD");
        $countryGD->setName("GRENADA");
        $countryGD->setNumcode("308");
        $countryGD->setPrintableName("Grenada");
        $manager->persist($countryGD);

        $countryGE = new Country();
        $countryGE->setIso("GE");
        $countryGE->setIso3("GEO");
        $countryGE->setName("GEORGIA");
        $countryGE->setNumcode("268");
        $countryGE->setPrintableName("Georgia");
        $manager->persist($countryGE);

        $countryGF = new Country();
        $countryGF->setIso("GF");
        $countryGF->setIso3("GUF");
        $countryGF->setName("FRENCH GUIANA");
        $countryGF->setNumcode("254");
        $countryGF->setPrintableName("French Guiana");
        $manager->persist($countryGF);

        $countryGH = new Country();
        $countryGH->setIso("GH");
        $countryGH->setIso3("GHA");
        $countryGH->setName("GHANA");
        $countryGH->setNumcode("288");
        $countryGH->setPrintableName("Ghana");
        $manager->persist($countryGH);

        $countryGI = new Country();
        $countryGI->setIso("GI");
        $countryGI->setIso3("GIB");
        $countryGI->setName("GIBRALTAR");
        $countryGI->setNumcode("292");
        $countryGI->setPrintableName("Gibraltar");
        $manager->persist($countryGI);

        $countryGL = new Country();
        $countryGL->setIso("GL");
        $countryGL->setIso3("GRL");
        $countryGL->setName("GREENLAND");
        $countryGL->setNumcode("304");
        $countryGL->setPrintableName("Greenland");
        $manager->persist($countryGL);

        $countryGM = new Country();
        $countryGM->setIso("GM");
        $countryGM->setIso3("GMB");
        $countryGM->setName("GAMBIA");
        $countryGM->setNumcode("270");
        $countryGM->setPrintableName("Gambia");
        $manager->persist($countryGM);

        $countryGN = new Country();
        $countryGN->setIso("GN");
        $countryGN->setIso3("GIN");
        $countryGN->setName("GUINEA");
        $countryGN->setNumcode("324");
        $countryGN->setPrintableName("Guinea");
        $manager->persist($countryGN);

        $countryGP = new Country();
        $countryGP->setIso("GP");
        $countryGP->setIso3("GLP");
        $countryGP->setName("GUADELOUPE");
        $countryGP->setNumcode("312");
        $countryGP->setPrintableName("Guadeloupe");
        $manager->persist($countryGP);

        $countryGQ = new Country();
        $countryGQ->setIso("GQ");
        $countryGQ->setIso3("GNQ");
        $countryGQ->setName("EQUATORIAL GUINEA");
        $countryGQ->setNumcode("226");
        $countryGQ->setPrintableName("Equatorial Guinea");
        $manager->persist($countryGQ);

        $countryGR = new Country();
        $countryGR->setIso("GR");
        $countryGR->setIso3("GRC");
        $countryGR->setName("GREECE");
        $countryGR->setNumcode("300");
        $countryGR->setPrintableName("Greece");
        $manager->persist($countryGR);

        $countryGS = new Country();
        $countryGS->setIso("GS");
        $countryGS->setName("SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS");
        $countryGS->setPrintableName("South Georgia and the South Sandwich Islands");
        $manager->persist($countryGS);

        $countryGT = new Country();
        $countryGT->setIso("GT");
        $countryGT->setIso3("GTM");
        $countryGT->setName("GUATEMALA");
        $countryGT->setNumcode("320");
        $countryGT->setPrintableName("Guatemala");
        $manager->persist($countryGT);

        $countryGU = new Country();
        $countryGU->setIso("GU");
        $countryGU->setIso3("GUM");
        $countryGU->setName("GUAM");
        $countryGU->setNumcode("316");
        $countryGU->setPrintableName("Guam");
        $manager->persist($countryGU);

        $countryGW = new Country();
        $countryGW->setIso("GW");
        $countryGW->setIso3("GNB");
        $countryGW->setName("GUINEA-BISSAU");
        $countryGW->setNumcode("624");
        $countryGW->setPrintableName("Guinea-Bissau");
        $manager->persist($countryGW);

        $countryGY = new Country();
        $countryGY->setIso("GY");
        $countryGY->setIso3("GUY");
        $countryGY->setName("GUYANA");
        $countryGY->setNumcode("328");
        $countryGY->setPrintableName("Guyana");
        $manager->persist($countryGY);

        $countryHK = new Country();
        $countryHK->setIso("HK");
        $countryHK->setIso3("HKG");
        $countryHK->setName("HONG KONG");
        $countryHK->setNumcode("344");
        $countryHK->setPrintableName("Hong Kong");
        $manager->persist($countryHK);

        $countryHM = new Country();
        $countryHM->setIso("HM");
        $countryHM->setName("HEARD ISLAND AND MCDONALD ISLANDS");
        $countryHM->setPrintableName("Heard Island and Mcdonald Islands");
        $manager->persist($countryHM);

        $countryHN = new Country();
        $countryHN->setIso("HN");
        $countryHN->setIso3("HND");
        $countryHN->setName("HONDURAS");
        $countryHN->setNumcode("340");
        $countryHN->setPrintableName("Honduras");
        $manager->persist($countryHN);

        $countryHR = new Country();
        $countryHR->setIso("HR");
        $countryHR->setIso3("HRV");
        $countryHR->setName("CROATIA");
        $countryHR->setNumcode("191");
        $countryHR->setPrintableName("Croatia");
        $manager->persist($countryHR);

        $countryHT = new Country();
        $countryHT->setIso("HT");
        $countryHT->setIso3("HTI");
        $countryHT->setName("HAITI");
        $countryHT->setNumcode("332");
        $countryHT->setPrintableName("Haiti");
        $manager->persist($countryHT);

        $countryHU = new Country();
        $countryHU->setIso("HU");
        $countryHU->setIso3("HUN");
        $countryHU->setName("HUNGARY");
        $countryHU->setNumcode("348");
        $countryHU->setPrintableName("Hungary");
        $manager->persist($countryHU);

        $countryID = new Country();
        $countryID->setIso("ID");
        $countryID->setIso3("IDN");
        $countryID->setName("INDONESIA");
        $countryID->setNumcode("360");
        $countryID->setPrintableName("Indonesia");
        $manager->persist($countryID);

        $countryIE = new Country();
        $countryIE->setIso("IE");
        $countryIE->setIso3("IRL");
        $countryIE->setName("IRELAND");
        $countryIE->setNumcode("372");
        $countryIE->setPrintableName("Ireland");
        $manager->persist($countryIE);

        $countryIL = new Country();
        $countryIL->setIso("IL");
        $countryIL->setIso3("ISR");
        $countryIL->setName("ISRAEL");
        $countryIL->setNumcode("376");
        $countryIL->setPrintableName("Israel");
        $manager->persist($countryIL);

        $countryIN = new Country();
        $countryIN->setIso("IN");
        $countryIN->setIso3("IND");
        $countryIN->setName("INDIA");
        $countryIN->setNumcode("356");
        $countryIN->setPrintableName("India");
        $manager->persist($countryIN);

        $countryIO = new Country();
        $countryIO->setIso("IO");
        $countryIO->setName("BRITISH INDIAN OCEAN TERRITORY");
        $countryIO->setPrintableName("British Indian Ocean Territory");
        $manager->persist($countryIO);

        $countryIQ = new Country();
        $countryIQ->setIso("IQ");
        $countryIQ->setIso3("IRQ");
        $countryIQ->setName("IRAQ");
        $countryIQ->setNumcode("368");
        $countryIQ->setPrintableName("Iraq");
        $manager->persist($countryIQ);

        $countryIR = new Country();
        $countryIR->setIso("IR");
        $countryIR->setIso3("IRN");
        $countryIR->setName("IRAN, ISLAMIC REPUBLIC OF");
        $countryIR->setNumcode("364");
        $countryIR->setPrintableName("Iran, Islamic Republic of");
        $manager->persist($countryIR);

        $countryIS = new Country();
        $countryIS->setIso("IS");
        $countryIS->setIso3("ISL");
        $countryIS->setName("ICELAND");
        $countryIS->setNumcode("352");
        $countryIS->setPrintableName("Iceland");
        $manager->persist($countryIS);

        $countryIT = new Country();
        $countryIT->setIso("IT");
        $countryIT->setIso3("ITA");
        $countryIT->setName("ITALY");
        $countryIT->setNumcode("380");
        $countryIT->setPrintableName("Italy");
        $manager->persist($countryIT);

        $countryJM = new Country();
        $countryJM->setIso("JM");
        $countryJM->setIso3("JAM");
        $countryJM->setName("JAMAICA");
        $countryJM->setNumcode("388");
        $countryJM->setPrintableName("Jamaica");
        $manager->persist($countryJM);

        $countryJO = new Country();
        $countryJO->setIso("JO");
        $countryJO->setIso3("JOR");
        $countryJO->setName("JORDAN");
        $countryJO->setNumcode("400");
        $countryJO->setPrintableName("Jordan");
        $manager->persist($countryJO);

        $countryJP = new Country();
        $countryJP->setIso("JP");
        $countryJP->setIso3("JPN");
        $countryJP->setName("JAPAN");
        $countryJP->setNumcode("392");
        $countryJP->setPrintableName("Japan");
        $manager->persist($countryJP);

        $countryKE = new Country();
        $countryKE->setIso("KE");
        $countryKE->setIso3("KEN");
        $countryKE->setName("KENYA");
        $countryKE->setNumcode("404");
        $countryKE->setPrintableName("Kenya");
        $manager->persist($countryKE);

        $countryKG = new Country();
        $countryKG->setIso("KG");
        $countryKG->setIso3("KGZ");
        $countryKG->setName("KYRGYZSTAN");
        $countryKG->setNumcode("417");
        $countryKG->setPrintableName("Kyrgyzstan");
        $manager->persist($countryKG);

        $countryKH = new Country();
        $countryKH->setIso("KH");
        $countryKH->setIso3("KHM");
        $countryKH->setName("CAMBODIA");
        $countryKH->setNumcode("116");
        $countryKH->setPrintableName("Cambodia");
        $manager->persist($countryKH);

        $countryKI = new Country();
        $countryKI->setIso("KI");
        $countryKI->setIso3("KIR");
        $countryKI->setName("KIRIBATI");
        $countryKI->setNumcode("296");
        $countryKI->setPrintableName("Kiribati");
        $manager->persist($countryKI);

        $countryKM = new Country();
        $countryKM->setIso("KM");
        $countryKM->setIso3("COM");
        $countryKM->setName("COMOROS");
        $countryKM->setNumcode("174");
        $countryKM->setPrintableName("Comoros");
        $manager->persist($countryKM);

        $countryKN = new Country();
        $countryKN->setIso("KN");
        $countryKN->setIso3("KNA");
        $countryKN->setName("SAINT KITTS AND NEVIS");
        $countryKN->setNumcode("659");
        $countryKN->setPrintableName("Saint Kitts and Nevis");
        $manager->persist($countryKN);

        $countryKP = new Country();
        $countryKP->setIso("KP");
        $countryKP->setIso3("PRK");
        $countryKP->setName("KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF");
        $countryKP->setNumcode("408");
        $countryKP->setPrintableName("Korea, Democratic People's Republic of");
        $manager->persist($countryKP);

        $countryKR = new Country();
        $countryKR->setIso("KR");
        $countryKR->setIso3("KOR");
        $countryKR->setName("KOREA, REPUBLIC OF");
        $countryKR->setNumcode("410");
        $countryKR->setPrintableName("Korea, Republic of");
        $manager->persist($countryKR);

        $countryKW = new Country();
        $countryKW->setIso("KW");
        $countryKW->setIso3("KWT");
        $countryKW->setName("KUWAIT");
        $countryKW->setNumcode("414");
        $countryKW->setPrintableName("Kuwait");
        $manager->persist($countryKW);

        $countryKY = new Country();
        $countryKY->setIso("KY");
        $countryKY->setIso3("CYM");
        $countryKY->setName("CAYMAN ISLANDS");
        $countryKY->setNumcode("136");
        $countryKY->setPrintableName("Cayman Islands");
        $manager->persist($countryKY);

        $countryKZ = new Country();
        $countryKZ->setIso("KZ");
        $countryKZ->setIso3("KAZ");
        $countryKZ->setName("KAZAKHSTAN");
        $countryKZ->setNumcode("398");
        $countryKZ->setPrintableName("Kazakhstan");
        $manager->persist($countryKZ);

        $countryLA = new Country();
        $countryLA->setIso("LA");
        $countryLA->setIso3("LAO");
        $countryLA->setName("LAO PEOPLE'S DEMOCRATIC REPUBLIC");
        $countryLA->setNumcode("418");
        $countryLA->setPrintableName("Lao People's Democratic Republic");
        $manager->persist($countryLA);

        $countryLB = new Country();
        $countryLB->setIso("LB");
        $countryLB->setIso3("LBN");
        $countryLB->setName("LEBANON");
        $countryLB->setNumcode("422");
        $countryLB->setPrintableName("Lebanon");
        $manager->persist($countryLB);

        $countryLC = new Country();
        $countryLC->setIso("LC");
        $countryLC->setIso3("LCA");
        $countryLC->setName("SAINT LUCIA");
        $countryLC->setNumcode("662");
        $countryLC->setPrintableName("Saint Lucia");
        $manager->persist($countryLC);

        $countryLI = new Country();
        $countryLI->setIso("LI");
        $countryLI->setIso3("LIE");
        $countryLI->setName("LIECHTENSTEIN");
        $countryLI->setNumcode("438");
        $countryLI->setPrintableName("Liechtenstein");
        $manager->persist($countryLI);

        $countryLK = new Country();
        $countryLK->setIso("LK");
        $countryLK->setIso3("LKA");
        $countryLK->setName("SRI LANKA");
        $countryLK->setNumcode("144");
        $countryLK->setPrintableName("Sri Lanka");
        $manager->persist($countryLK);

        $countryLR = new Country();
        $countryLR->setIso("LR");
        $countryLR->setIso3("LBR");
        $countryLR->setName("LIBERIA");
        $countryLR->setNumcode("430");
        $countryLR->setPrintableName("Liberia");
        $manager->persist($countryLR);

        $countryLS = new Country();
        $countryLS->setIso("LS");
        $countryLS->setIso3("LSO");
        $countryLS->setName("LESOTHO");
        $countryLS->setNumcode("426");
        $countryLS->setPrintableName("Lesotho");
        $manager->persist($countryLS);

        $countryLT = new Country();
        $countryLT->setIso("LT");
        $countryLT->setIso3("LTU");
        $countryLT->setName("LITHUANIA");
        $countryLT->setNumcode("440");
        $countryLT->setPrintableName("Lithuania");
        $manager->persist($countryLT);

        $countryLU = new Country();
        $countryLU->setIso("LU");
        $countryLU->setIso3("LUX");
        $countryLU->setName("LUXEMBOURG");
        $countryLU->setNumcode("442");
        $countryLU->setPrintableName("Luxembourg");
        $manager->persist($countryLU);

        $countryLV = new Country();
        $countryLV->setIso("LV");
        $countryLV->setIso3("LVA");
        $countryLV->setName("LATVIA");
        $countryLV->setNumcode("428");
        $countryLV->setPrintableName("Latvia");
        $manager->persist($countryLV);

        $countryLY = new Country();
        $countryLY->setIso("LY");
        $countryLY->setIso3("LBY");
        $countryLY->setName("LIBYAN ARAB JAMAHIRIYA");
        $countryLY->setNumcode("434");
        $countryLY->setPrintableName("Libyan Arab Jamahiriya");
        $manager->persist($countryLY);

        $countryMA = new Country();
        $countryMA->setIso("MA");
        $countryMA->setIso3("MAR");
        $countryMA->setName("MOROCCO");
        $countryMA->setNumcode("504");
        $countryMA->setPrintableName("Morocco");
        $manager->persist($countryMA);

        $countryMC = new Country();
        $countryMC->setIso("MC");
        $countryMC->setIso3("MCO");
        $countryMC->setName("MONACO");
        $countryMC->setNumcode("492");
        $countryMC->setPrintableName("Monaco");
        $manager->persist($countryMC);

        $countryMD = new Country();
        $countryMD->setIso("MD");
        $countryMD->setIso3("MDA");
        $countryMD->setName("MOLDOVA, REPUBLIC OF");
        $countryMD->setNumcode("498");
        $countryMD->setPrintableName("Moldova, Republic of");
        $manager->persist($countryMD);

        $countryMG = new Country();
        $countryMG->setIso("MG");
        $countryMG->setIso3("MDG");
        $countryMG->setName("MADAGASCAR");
        $countryMG->setNumcode("450");
        $countryMG->setPrintableName("Madagascar");
        $manager->persist($countryMG);

        $countryMH = new Country();
        $countryMH->setIso("MH");
        $countryMH->setIso3("MHL");
        $countryMH->setName("MARSHALL ISLANDS");
        $countryMH->setNumcode("584");
        $countryMH->setPrintableName("Marshall Islands");
        $manager->persist($countryMH);

        $countryMK = new Country();
        $countryMK->setIso("MK");
        $countryMK->setIso3("MKD");
        $countryMK->setName("MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF");
        $countryMK->setNumcode("807");
        $countryMK->setPrintableName("Macedonia, the Former Yugoslav Republic of");
        $manager->persist($countryMK);

        $countryML = new Country();
        $countryML->setIso("ML");
        $countryML->setIso3("MLI");
        $countryML->setName("MALI");
        $countryML->setNumcode("466");
        $countryML->setPrintableName("Mali");
        $manager->persist($countryML);

        $countryMM = new Country();
        $countryMM->setIso("MM");
        $countryMM->setIso3("MMR");
        $countryMM->setName("MYANMAR");
        $countryMM->setNumcode("104");
        $countryMM->setPrintableName("Myanmar");
        $manager->persist($countryMM);

        $countryMN = new Country();
        $countryMN->setIso("MN");
        $countryMN->setIso3("MNG");
        $countryMN->setName("MONGOLIA");
        $countryMN->setNumcode("496");
        $countryMN->setPrintableName("Mongolia");
        $manager->persist($countryMN);

        $countryMO = new Country();
        $countryMO->setIso("MO");
        $countryMO->setIso3("MAC");
        $countryMO->setName("MACAO");
        $countryMO->setNumcode("446");
        $countryMO->setPrintableName("Macao");
        $manager->persist($countryMO);

        $countryMP = new Country();
        $countryMP->setIso("MP");
        $countryMP->setIso3("MNP");
        $countryMP->setName("NORTHERN MARIANA ISLANDS");
        $countryMP->setNumcode("580");
        $countryMP->setPrintableName("Northern Mariana Islands");
        $manager->persist($countryMP);

        $countryMQ = new Country();
        $countryMQ->setIso("MQ");
        $countryMQ->setIso3("MTQ");
        $countryMQ->setName("MARTINIQUE");
        $countryMQ->setNumcode("474");
        $countryMQ->setPrintableName("Martinique");
        $manager->persist($countryMQ);

        $countryMR = new Country();
        $countryMR->setIso("MR");
        $countryMR->setIso3("MRT");
        $countryMR->setName("MAURITANIA");
        $countryMR->setNumcode("478");
        $countryMR->setPrintableName("Mauritania");
        $manager->persist($countryMR);

        $countryMS = new Country();
        $countryMS->setIso("MS");
        $countryMS->setIso3("MSR");
        $countryMS->setName("MONTSERRAT");
        $countryMS->setNumcode("500");
        $countryMS->setPrintableName("Montserrat");
        $manager->persist($countryMS);

        $countryMT = new Country();
        $countryMT->setIso("MT");
        $countryMT->setIso3("MLT");
        $countryMT->setName("MALTA");
        $countryMT->setNumcode("470");
        $countryMT->setPrintableName("Malta");
        $manager->persist($countryMT);

        $countryMU = new Country();
        $countryMU->setIso("MU");
        $countryMU->setIso3("MUS");
        $countryMU->setName("MAURITIUS");
        $countryMU->setNumcode("480");
        $countryMU->setPrintableName("Mauritius");
        $manager->persist($countryMU);

        $countryMV = new Country();
        $countryMV->setIso("MV");
        $countryMV->setIso3("MDV");
        $countryMV->setName("MALDIVES");
        $countryMV->setNumcode("462");
        $countryMV->setPrintableName("Maldives");
        $manager->persist($countryMV);

        $countryMW = new Country();
        $countryMW->setIso("MW");
        $countryMW->setIso3("MWI");
        $countryMW->setName("MALAWI");
        $countryMW->setNumcode("454");
        $countryMW->setPrintableName("Malawi");
        $manager->persist($countryMW);

        $countryMX = new Country();
        $countryMX->setIso("MX");
        $countryMX->setIso3("MEX");
        $countryMX->setName("MEXICO");
        $countryMX->setNumcode("484");
        $countryMX->setPrintableName("Mexico");
        $manager->persist($countryMX);

        $countryMY = new Country();
        $countryMY->setIso("MY");
        $countryMY->setIso3("MYS");
        $countryMY->setName("MALAYSIA");
        $countryMY->setNumcode("458");
        $countryMY->setPrintableName("Malaysia");
        $manager->persist($countryMY);

        $countryMZ = new Country();
        $countryMZ->setIso("MZ");
        $countryMZ->setIso3("MOZ");
        $countryMZ->setName("MOZAMBIQUE");
        $countryMZ->setNumcode("508");
        $countryMZ->setPrintableName("Mozambique");
        $manager->persist($countryMZ);

        $countryNA = new Country();
        $countryNA->setIso("NA");
        $countryNA->setIso3("NAM");
        $countryNA->setName("NAMIBIA");
        $countryNA->setNumcode("516");
        $countryNA->setPrintableName("Namibia");
        $manager->persist($countryNA);

        $countryNC = new Country();
        $countryNC->setIso("NC");
        $countryNC->setIso3("NCL");
        $countryNC->setName("NEW CALEDONIA");
        $countryNC->setNumcode("540");
        $countryNC->setPrintableName("New Caledonia");
        $manager->persist($countryNC);

        $countryNE = new Country();
        $countryNE->setIso("NE");
        $countryNE->setIso3("NER");
        $countryNE->setName("NIGER");
        $countryNE->setNumcode("562");
        $countryNE->setPrintableName("Niger");
        $manager->persist($countryNE);

        $countryNF = new Country();
        $countryNF->setIso("NF");
        $countryNF->setIso3("NFK");
        $countryNF->setName("NORFOLK ISLAND");
        $countryNF->setNumcode("574");
        $countryNF->setPrintableName("Norfolk Island");
        $manager->persist($countryNF);

        $countryNG = new Country();
        $countryNG->setIso("NG");
        $countryNG->setIso3("NGA");
        $countryNG->setName("NIGERIA");
        $countryNG->setNumcode("566");
        $countryNG->setPrintableName("Nigeria");
        $manager->persist($countryNG);

        $countryNI = new Country();
        $countryNI->setIso("NI");
        $countryNI->setIso3("NIC");
        $countryNI->setName("NICARAGUA");
        $countryNI->setNumcode("558");
        $countryNI->setPrintableName("Nicaragua");
        $manager->persist($countryNI);

        $countryNL = new Country();
        $countryNL->setIso("NL");
        $countryNL->setIso3("NLD");
        $countryNL->setName("NETHERLANDS");
        $countryNL->setNumcode("528");
        $countryNL->setPrintableName("Netherlands");
        $manager->persist($countryNL);

        $countryNO = new Country();
        $countryNO->setIso("NO");
        $countryNO->setIso3("NOR");
        $countryNO->setName("NORWAY");
        $countryNO->setNumcode("578");
        $countryNO->setPrintableName("Norway");
        $manager->persist($countryNO);

        $countryNP = new Country();
        $countryNP->setIso("NP");
        $countryNP->setIso3("NPL");
        $countryNP->setName("NEPAL");
        $countryNP->setNumcode("524");
        $countryNP->setPrintableName("Nepal");
        $manager->persist($countryNP);

        $countryNR = new Country();
        $countryNR->setIso("NR");
        $countryNR->setIso3("NRU");
        $countryNR->setName("NAURU");
        $countryNR->setNumcode("520");
        $countryNR->setPrintableName("Nauru");
        $manager->persist($countryNR);

        $countryNU = new Country();
        $countryNU->setIso("NU");
        $countryNU->setIso3("NIU");
        $countryNU->setName("NIUE");
        $countryNU->setNumcode("570");
        $countryNU->setPrintableName("Niue");
        $manager->persist($countryNU);

        $countryNZ = new Country();
        $countryNZ->setIso("NZ");
        $countryNZ->setIso3("NZL");
        $countryNZ->setName("NEW ZEALAND");
        $countryNZ->setNumcode("554");
        $countryNZ->setPrintableName("New Zealand");
        $manager->persist($countryNZ);

        $countryOM = new Country();
        $countryOM->setIso("OM");
        $countryOM->setIso3("OMN");
        $countryOM->setName("OMAN");
        $countryOM->setNumcode("512");
        $countryOM->setPrintableName("Oman");
        $manager->persist($countryOM);

        $countryPA = new Country();
        $countryPA->setIso("PA");
        $countryPA->setIso3("PAN");
        $countryPA->setName("PANAMA");
        $countryPA->setNumcode("591");
        $countryPA->setPrintableName("Panama");
        $manager->persist($countryPA);

        $countryPE = new Country();
        $countryPE->setIso("PE");
        $countryPE->setIso3("PER");
        $countryPE->setName("PERU");
        $countryPE->setNumcode("604");
        $countryPE->setPrintableName("Peru");
        $manager->persist($countryPE);

        $countryPF = new Country();
        $countryPF->setIso("PF");
        $countryPF->setIso3("PYF");
        $countryPF->setName("FRENCH POLYNESIA");
        $countryPF->setNumcode("258");
        $countryPF->setPrintableName("French Polynesia");
        $manager->persist($countryPF);

        $countryPG = new Country();
        $countryPG->setIso("PG");
        $countryPG->setIso3("PNG");
        $countryPG->setName("PAPUA NEW GUINEA");
        $countryPG->setNumcode("598");
        $countryPG->setPrintableName("Papua New Guinea");
        $manager->persist($countryPG);

        $countryPH = new Country();
        $countryPH->setIso("PH");
        $countryPH->setIso3("PHL");
        $countryPH->setName("PHILIPPINES");
        $countryPH->setNumcode("608");
        $countryPH->setPrintableName("Philippines");
        $manager->persist($countryPH);

        $countryPK = new Country();
        $countryPK->setIso("PK");
        $countryPK->setIso3("PAK");
        $countryPK->setName("PAKISTAN");
        $countryPK->setNumcode("586");
        $countryPK->setPrintableName("Pakistan");
        $manager->persist($countryPK);

        $countryPL = new Country();
        $countryPL->setIso("PL");
        $countryPL->setIso3("POL");
        $countryPL->setName("POLAND");
        $countryPL->setNumcode("616");
        $countryPL->setPrintableName("Poland");
        $manager->persist($countryPL);

        $countryPM = new Country();
        $countryPM->setIso("PM");
        $countryPM->setIso3("SPM");
        $countryPM->setName("SAINT PIERRE AND MIQUELON");
        $countryPM->setNumcode("666");
        $countryPM->setPrintableName("Saint Pierre and Miquelon");
        $manager->persist($countryPM);

        $countryPN = new Country();
        $countryPN->setIso("PN");
        $countryPN->setIso3("PCN");
        $countryPN->setName("PITCAIRN");
        $countryPN->setNumcode("612");
        $countryPN->setPrintableName("Pitcairn");
        $manager->persist($countryPN);

        $countryPR = new Country();
        $countryPR->setIso("PR");
        $countryPR->setIso3("PRI");
        $countryPR->setName("PUERTO RICO");
        $countryPR->setNumcode("630");
        $countryPR->setPrintableName("Puerto Rico");
        $manager->persist($countryPR);

        $countryPS = new Country();
        $countryPS->setIso("PS");
        $countryPS->setName("PALESTINIAN TERRITORY, OCCUPIED");
        $countryPS->setPrintableName("Palestinian Territory, Occupied");
        $manager->persist($countryPS);

        $countryPT = new Country();
        $countryPT->setIso("PT");
        $countryPT->setIso3("PRT");
        $countryPT->setName("PORTUGAL");
        $countryPT->setNumcode("620");
        $countryPT->setPrintableName("Portugal");
        $manager->persist($countryPT);

        $countryPW = new Country();
        $countryPW->setIso("PW");
        $countryPW->setIso3("PLW");
        $countryPW->setName("PALAU");
        $countryPW->setNumcode("585");
        $countryPW->setPrintableName("Palau");
        $manager->persist($countryPW);

        $countryPY = new Country();
        $countryPY->setIso("PY");
        $countryPY->setIso3("PRY");
        $countryPY->setName("PARAGUAY");
        $countryPY->setNumcode("600");
        $countryPY->setPrintableName("Paraguay");
        $manager->persist($countryPY);

        $countryQA = new Country();
        $countryQA->setIso("QA");
        $countryQA->setIso3("QAT");
        $countryQA->setName("QATAR");
        $countryQA->setNumcode("634");
        $countryQA->setPrintableName("Qatar");
        $manager->persist($countryQA);

        $countryRE = new Country();
        $countryRE->setIso("RE");
        $countryRE->setIso3("REU");
        $countryRE->setName("REUNION");
        $countryRE->setNumcode("638");
        $countryRE->setPrintableName("Reunion");
        $manager->persist($countryRE);

        $countryRO = new Country();
        $countryRO->setIso("RO");
        $countryRO->setIso3("ROM");
        $countryRO->setName("ROMANIA");
        $countryRO->setNumcode("642");
        $countryRO->setPrintableName("Romania");
        $manager->persist($countryRO);

        $countryRU = new Country();
        $countryRU->setIso("RU");
        $countryRU->setIso3("RUS");
        $countryRU->setName("RUSSIAN FEDERATION");
        $countryRU->setNumcode("643");
        $countryRU->setPrintableName("Russian Federation");
        $manager->persist($countryRU);

        $countryRW = new Country();
        $countryRW->setIso("RW");
        $countryRW->setIso3("RWA");
        $countryRW->setName("RWANDA");
        $countryRW->setNumcode("646");
        $countryRW->setPrintableName("Rwanda");
        $manager->persist($countryRW);

        $countrySA = new Country();
        $countrySA->setIso("SA");
        $countrySA->setIso3("SAU");
        $countrySA->setName("SAUDI ARABIA");
        $countrySA->setNumcode("682");
        $countrySA->setPrintableName("Saudi Arabia");
        $manager->persist($countrySA);

        $countrySB = new Country();
        $countrySB->setIso("SB");
        $countrySB->setIso3("SLB");
        $countrySB->setName("SOLOMON ISLANDS");
        $countrySB->setNumcode("90");
        $countrySB->setPrintableName("Solomon Islands");
        $manager->persist($countrySB);

        $countrySC = new Country();
        $countrySC->setIso("SC");
        $countrySC->setIso3("SYC");
        $countrySC->setName("SEYCHELLES");
        $countrySC->setNumcode("690");
        $countrySC->setPrintableName("Seychelles");
        $manager->persist($countrySC);

        $countrySD = new Country();
        $countrySD->setIso("SD");
        $countrySD->setIso3("SDN");
        $countrySD->setName("SUDAN");
        $countrySD->setNumcode("736");
        $countrySD->setPrintableName("Sudan");
        $manager->persist($countrySD);

        $countrySE = new Country();
        $countrySE->setIso("SE");
        $countrySE->setIso3("SWE");
        $countrySE->setName("SWEDEN");
        $countrySE->setNumcode("752");
        $countrySE->setPrintableName("Sweden");
        $manager->persist($countrySE);

        $countrySG = new Country();
        $countrySG->setIso("SG");
        $countrySG->setIso3("SGP");
        $countrySG->setName("SINGAPORE");
        $countrySG->setNumcode("702");
        $countrySG->setPrintableName("Singapore");
        $manager->persist($countrySG);

        $countrySH = new Country();
        $countrySH->setIso("SH");
        $countrySH->setIso3("SHN");
        $countrySH->setName("SAINT HELENA");
        $countrySH->setNumcode("654");
        $countrySH->setPrintableName("Saint Helena");
        $manager->persist($countrySH);

        $countrySI = new Country();
        $countrySI->setIso("SI");
        $countrySI->setIso3("SVN");
        $countrySI->setName("SLOVENIA");
        $countrySI->setNumcode("705");
        $countrySI->setPrintableName("Slovenia");
        $manager->persist($countrySI);

        $countrySJ = new Country();
        $countrySJ->setIso("SJ");
        $countrySJ->setIso3("SJM");
        $countrySJ->setName("SVALBARD AND JAN MAYEN");
        $countrySJ->setNumcode("744");
        $countrySJ->setPrintableName("Svalbard and Jan Mayen");
        $manager->persist($countrySJ);

        $countrySK = new Country();
        $countrySK->setIso("SK");
        $countrySK->setIso3("SVK");
        $countrySK->setName("SLOVAKIA");
        $countrySK->setNumcode("703");
        $countrySK->setPrintableName("Slovakia");
        $manager->persist($countrySK);

        $countrySL = new Country();
        $countrySL->setIso("SL");
        $countrySL->setIso3("SLE");
        $countrySL->setName("SIERRA LEONE");
        $countrySL->setNumcode("694");
        $countrySL->setPrintableName("Sierra Leone");
        $manager->persist($countrySL);

        $countrySM = new Country();
        $countrySM->setIso("SM");
        $countrySM->setIso3("SMR");
        $countrySM->setName("SAN MARINO");
        $countrySM->setNumcode("674");
        $countrySM->setPrintableName("San Marino");
        $manager->persist($countrySM);

        $countrySN = new Country();
        $countrySN->setIso("SN");
        $countrySN->setIso3("SEN");
        $countrySN->setName("SENEGAL");
        $countrySN->setNumcode("686");
        $countrySN->setPrintableName("Senegal");
        $manager->persist($countrySN);

        $countrySO = new Country();
        $countrySO->setIso("SO");
        $countrySO->setIso3("SOM");
        $countrySO->setName("SOMALIA");
        $countrySO->setNumcode("706");
        $countrySO->setPrintableName("Somalia");
        $manager->persist($countrySO);

        $countrySR = new Country();
        $countrySR->setIso("SR");
        $countrySR->setIso3("SUR");
        $countrySR->setName("SURINAME");
        $countrySR->setNumcode("740");
        $countrySR->setPrintableName("Suriname");
        $manager->persist($countrySR);

        $countryST = new Country();
        $countryST->setIso("ST");
        $countryST->setIso3("STP");
        $countryST->setName("SAO TOME AND PRINCIPE");
        $countryST->setNumcode("678");
        $countryST->setPrintableName("Sao Tome and Principe");
        $manager->persist($countryST);

        $countrySV = new Country();
        $countrySV->setIso("SV");
        $countrySV->setIso3("SLV");
        $countrySV->setName("EL SALVADOR");
        $countrySV->setNumcode("222");
        $countrySV->setPrintableName("El Salvador");
        $manager->persist($countrySV);

        $countrySY = new Country();
        $countrySY->setIso("SY");
        $countrySY->setIso3("SYR");
        $countrySY->setName("SYRIAN ARAB REPUBLIC");
        $countrySY->setNumcode("760");
        $countrySY->setPrintableName("Syrian Arab Republic");
        $manager->persist($countrySY);

        $countrySZ = new Country();
        $countrySZ->setIso("SZ");
        $countrySZ->setIso3("SWZ");
        $countrySZ->setName("SWAZILAND");
        $countrySZ->setNumcode("748");
        $countrySZ->setPrintableName("Swaziland");
        $manager->persist($countrySZ);

        $countryTC = new Country();
        $countryTC->setIso("TC");
        $countryTC->setIso3("TCA");
        $countryTC->setName("TURKS AND CAICOS ISLANDS");
        $countryTC->setNumcode("796");
        $countryTC->setPrintableName("Turks and Caicos Islands");
        $manager->persist($countryTC);

        $countryTD = new Country();
        $countryTD->setIso("TD");
        $countryTD->setIso3("TCD");
        $countryTD->setName("CHAD");
        $countryTD->setNumcode("148");
        $countryTD->setPrintableName("Chad");
        $manager->persist($countryTD);

        $countryTF = new Country();
        $countryTF->setIso("TF");
        $countryTF->setName("FRENCH SOUTHERN TERRITORIES");
        $countryTF->setPrintableName("French Southern Territories");
        $manager->persist($countryTF);

        $countryTG = new Country();
        $countryTG->setIso("TG");
        $countryTG->setIso3("TGO");
        $countryTG->setName("TOGO");
        $countryTG->setNumcode("768");
        $countryTG->setPrintableName("Togo");
        $manager->persist($countryTG);

        $countryTH = new Country();
        $countryTH->setIso("TH");
        $countryTH->setIso3("THA");
        $countryTH->setName("THAILAND");
        $countryTH->setNumcode("764");
        $countryTH->setPrintableName("Thailand");
        $manager->persist($countryTH);

        $countryTJ = new Country();
        $countryTJ->setIso("TJ");
        $countryTJ->setIso3("TJK");
        $countryTJ->setName("TAJIKISTAN");
        $countryTJ->setNumcode("762");
        $countryTJ->setPrintableName("Tajikistan");
        $manager->persist($countryTJ);

        $countryTK = new Country();
        $countryTK->setIso("TK");
        $countryTK->setIso3("TKL");
        $countryTK->setName("TOKELAU");
        $countryTK->setNumcode("772");
        $countryTK->setPrintableName("Tokelau");
        $manager->persist($countryTK);

        $countryTL = new Country();
        $countryTL->setIso("TL");
        $countryTL->setName("TIMOR-LESTE");
        $countryTL->setPrintableName("Timor-Leste");
        $manager->persist($countryTL);

        $countryTM = new Country();
        $countryTM->setIso("TM");
        $countryTM->setIso3("TKM");
        $countryTM->setName("TURKMENISTAN");
        $countryTM->setNumcode("795");
        $countryTM->setPrintableName("Turkmenistan");
        $manager->persist($countryTM);

        $countryTN = new Country();
        $countryTN->setIso("TN");
        $countryTN->setIso3("TUN");
        $countryTN->setName("TUNISIA");
        $countryTN->setNumcode("788");
        $countryTN->setPrintableName("Tunisia");
        $manager->persist($countryTN);

        $countryTO = new Country();
        $countryTO->setIso("TO");
        $countryTO->setIso3("TON");
        $countryTO->setName("TONGA");
        $countryTO->setNumcode("776");
        $countryTO->setPrintableName("Tonga");
        $manager->persist($countryTO);

        $countryTR = new Country();
        $countryTR->setIso("TR");
        $countryTR->setIso3("TUR");
        $countryTR->setName("TURKEY");
        $countryTR->setNumcode("792");
        $countryTR->setPrintableName("Turkey");
        $manager->persist($countryTR);

        $countryTT = new Country();
        $countryTT->setIso("TT");
        $countryTT->setIso3("TTO");
        $countryTT->setName("TRINIDAD AND TOBAGO");
        $countryTT->setNumcode("780");
        $countryTT->setPrintableName("Trinidad and Tobago");
        $manager->persist($countryTT);

        $countryTV = new Country();
        $countryTV->setIso("TV");
        $countryTV->setIso3("TUV");
        $countryTV->setName("TUVALU");
        $countryTV->setNumcode("798");
        $countryTV->setPrintableName("Tuvalu");
        $manager->persist($countryTV);

        $countryTW = new Country();
        $countryTW->setIso("TW");
        $countryTW->setIso3("TWN");
        $countryTW->setName("TAIWAN, PROVINCE OF CHINA");
        $countryTW->setNumcode("158");
        $countryTW->setPrintableName("Taiwan, Province of China");
        $manager->persist($countryTW);

        $countryTZ = new Country();
        $countryTZ->setIso("TZ");
        $countryTZ->setIso3("TZA");
        $countryTZ->setName("TANZANIA, UNITED REPUBLIC OF");
        $countryTZ->setNumcode("834");
        $countryTZ->setPrintableName("Tanzania, United Republic of");
        $manager->persist($countryTZ);

        $countryUA = new Country();
        $countryUA->setIso("UA");
        $countryUA->setIso3("UKR");
        $countryUA->setName("UKRAINE");
        $countryUA->setNumcode("804");
        $countryUA->setPrintableName("Ukraine");
        $manager->persist($countryUA);

        $countryUG = new Country();
        $countryUG->setIso("UG");
        $countryUG->setIso3("UGA");
        $countryUG->setName("UGANDA");
        $countryUG->setNumcode("800");
        $countryUG->setPrintableName("Uganda");
        $manager->persist($countryUG);

        $countryUM = new Country();
        $countryUM->setIso("UM");
        $countryUM->setName("UNITED STATES MINOR OUTLYING ISLANDS");
        $countryUM->setPrintableName("United States Minor Outlying Islands");
        $manager->persist($countryUM);

        $countryUS = new Country();
        $countryUS->setIso("US");
        $countryUS->setIso3("USA");
        $countryUS->setName("UNITED STATES");
        $countryUS->setNumcode("840");
        $countryUS->setPrintableName("United States");
        $manager->persist($countryUS);

        $countryUY = new Country();
        $countryUY->setIso("UY");
        $countryUY->setIso3("URY");
        $countryUY->setName("URUGUAY");
        $countryUY->setNumcode("858");
        $countryUY->setPrintableName("Uruguay");
        $manager->persist($countryUY);

        $countryUZ = new Country();
        $countryUZ->setIso("UZ");
        $countryUZ->setIso3("UZB");
        $countryUZ->setName("UZBEKISTAN");
        $countryUZ->setNumcode("860");
        $countryUZ->setPrintableName("Uzbekistan");
        $manager->persist($countryUZ);

        $countryVA = new Country();
        $countryVA->setIso("VA");
        $countryVA->setIso3("VAT");
        $countryVA->setName("HOLY SEE (VATICAN CITY STATE)");
        $countryVA->setNumcode("336");
        $countryVA->setPrintableName("Holy See (Vatican City State)");
        $manager->persist($countryVA);

        $countryVC = new Country();
        $countryVC->setIso("VC");
        $countryVC->setIso3("VCT");
        $countryVC->setName("SAINT VINCENT AND THE GRENADINES");
        $countryVC->setNumcode("670");
        $countryVC->setPrintableName("Saint Vincent and the Grenadines");
        $manager->persist($countryVC);

        $countryVE = new Country();
        $countryVE->setIso("VE");
        $countryVE->setIso3("VEN");
        $countryVE->setName("VENEZUELA");
        $countryVE->setNumcode("862");
        $countryVE->setPrintableName("Venezuela");
        $manager->persist($countryVE);

        $countryVG = new Country();
        $countryVG->setIso("VG");
        $countryVG->setIso3("VGB");
        $countryVG->setName("VIRGIN ISLANDS, BRITISH");
        $countryVG->setNumcode("92");
        $countryVG->setPrintableName("Virgin Islands, British");
        $manager->persist($countryVG);

        $countryVI = new Country();
        $countryVI->setIso("VI");
        $countryVI->setIso3("VIR");
        $countryVI->setName("VIRGIN ISLANDS, U.S.");
        $countryVI->setNumcode("850");
        $countryVI->setPrintableName("Virgin Islands, U.s.");
        $manager->persist($countryVI);

        $countryVN = new Country();
        $countryVN->setIso("VN");
        $countryVN->setIso3("VNM");
        $countryVN->setName("VIET NAM");
        $countryVN->setNumcode("704");
        $countryVN->setPrintableName("Viet Nam");
        $manager->persist($countryVN);

        $countryVU = new Country();
        $countryVU->setIso("VU");
        $countryVU->setIso3("VUT");
        $countryVU->setName("VANUATU");
        $countryVU->setNumcode("548");
        $countryVU->setPrintableName("Vanuatu");
        $manager->persist($countryVU);

        $countryWF = new Country();
        $countryWF->setIso("WF");
        $countryWF->setIso3("WLF");
        $countryWF->setName("WALLIS AND FUTUNA");
        $countryWF->setNumcode("876");
        $countryWF->setPrintableName("Wallis and Futuna");
        $manager->persist($countryWF);

        $countryWS = new Country();
        $countryWS->setIso("WS");
        $countryWS->setIso3("WSM");
        $countryWS->setName("SAMOA");
        $countryWS->setNumcode("882");
        $countryWS->setPrintableName("Samoa");
        $manager->persist($countryWS);

        $countryYE = new Country();
        $countryYE->setIso("YE");
        $countryYE->setIso3("YEM");
        $countryYE->setName("YEMEN");
        $countryYE->setNumcode("887");
        $countryYE->setPrintableName("Yemen");
        $manager->persist($countryYE);

        $countryYT = new Country();
        $countryYT->setIso("YT");
        $countryYT->setName("MAYOTTE");
        $countryYT->setPrintableName("Mayotte");
        $manager->persist($countryYT);

        $countryZA = new Country();
        $countryZA->setIso("ZA");
        $countryZA->setIso3("ZAF");
        $countryZA->setName("SOUTH AFRICA");
        $countryZA->setNumcode("710");
        $countryZA->setPrintableName("South Africa");
        $manager->persist($countryZA);

        $countryZM = new Country();
        $countryZM->setIso("ZM");
        $countryZM->setIso3("ZMB");
        $countryZM->setName("ZAMBIA");
        $countryZM->setNumcode("894");
        $countryZM->setPrintableName("Zambia");
        $manager->persist($countryZM);

        $countryZW = new Country();
        $countryZW->setIso("ZW");
        $countryZW->setIso3("ZWE");
        $countryZW->setName("ZIMBABWE");
        $countryZW->setNumcode("716");
        $countryZW->setPrintableName("Zimbabwe");
        $manager->persist($countryZW);

        $manager->flush();
        
        $this->addReference("countryAD", $countryAD);
        $this->addReference("countryAE", $countryAE);
        $this->addReference("countryAF", $countryAF);
        $this->addReference("countryAG", $countryAG);
        $this->addReference("countryAI", $countryAI);
        $this->addReference("countryAL", $countryAL);
        $this->addReference("countryAM", $countryAM);
        $this->addReference("countryAN", $countryAN);
        $this->addReference("countryAO", $countryAO);
        $this->addReference("countryAQ", $countryAQ);
        $this->addReference("countryAR", $countryAR);
        $this->addReference("countryAS", $countryAS);
        $this->addReference("countryAT", $countryAT);
        $this->addReference("countryAU", $countryAU);
        $this->addReference("countryAW", $countryAW);
        $this->addReference("countryAZ", $countryAZ);
        $this->addReference("countryBA", $countryBA);
        $this->addReference("countryBB", $countryBB);
        $this->addReference("countryBD", $countryBD);
        $this->addReference("countryBE", $countryBE);
        $this->addReference("countryBF", $countryBF);
        $this->addReference("countryBG", $countryBG);
        $this->addReference("countryBH", $countryBH);
        $this->addReference("countryBI", $countryBI);
        $this->addReference("countryBJ", $countryBJ);
        $this->addReference("countryBM", $countryBM);
        $this->addReference("countryBN", $countryBN);
        $this->addReference("countryBO", $countryBO);
        $this->addReference("countryBR", $countryBR);
        $this->addReference("countryBS", $countryBS);
        $this->addReference("countryBT", $countryBT);
        $this->addReference("countryBV", $countryBV);
        $this->addReference("countryBW", $countryBW);
        $this->addReference("countryBY", $countryBY);
        $this->addReference("countryBZ", $countryBZ);
        $this->addReference("countryCA", $countryCA);
        $this->addReference("countryCC", $countryCC);
        $this->addReference("countryCD", $countryCD);
        $this->addReference("countryCF", $countryCF);
        $this->addReference("countryCG", $countryCG);
        $this->addReference("countryCH", $countryCH);
        $this->addReference("countryCI", $countryCI);
        $this->addReference("countryCK", $countryCK);
        $this->addReference("countryCL", $countryCL);
        $this->addReference("countryCM", $countryCM);
        $this->addReference("countryCN", $countryCN);
        $this->addReference("countryCO", $countryCO);
        $this->addReference("countryCR", $countryCR);
        $this->addReference("countryCS", $countryCS);
        $this->addReference("countryCU", $countryCU);
        $this->addReference("countryCV", $countryCV);
        $this->addReference("countryCX", $countryCX);
        $this->addReference("countryCY", $countryCY);
        $this->addReference("countryCZ", $countryCZ);
        $this->addReference("countryDE", $countryDE);
        $this->addReference("countryDJ", $countryDJ);
        $this->addReference("countryDK", $countryDK);
        $this->addReference("countryDM", $countryDM);
        $this->addReference("countryDO", $countryDO);
        $this->addReference("countryDZ", $countryDZ);
        $this->addReference("countryEC", $countryEC);
        $this->addReference("countryEE", $countryEE);
        $this->addReference("countryEG", $countryEG);
        $this->addReference("countryEH", $countryEH);
        $this->addReference("countryER", $countryER);
        $this->addReference("countryES", $countryES);
        $this->addReference("countryET", $countryET);
        $this->addReference("countryFI", $countryFI);
        $this->addReference("countryFJ", $countryFJ);
        $this->addReference("countryFK", $countryFK);
        $this->addReference("countryFM", $countryFM);
        $this->addReference("countryFO", $countryFO);
        $this->addReference("countryFR", $countryFR);
        $this->addReference("countryGA", $countryGA);
        $this->addReference("countryGB", $countryGB);
        $this->addReference("countryGD", $countryGD);
        $this->addReference("countryGE", $countryGE);
        $this->addReference("countryGF", $countryGF);
        $this->addReference("countryGH", $countryGH);
        $this->addReference("countryGI", $countryGI);
        $this->addReference("countryGL", $countryGL);
        $this->addReference("countryGM", $countryGM);
        $this->addReference("countryGN", $countryGN);
        $this->addReference("countryGP", $countryGP);
        $this->addReference("countryGQ", $countryGQ);
        $this->addReference("countryGR", $countryGR);
        $this->addReference("countryGS", $countryGS);
        $this->addReference("countryGT", $countryGT);
        $this->addReference("countryGU", $countryGU);
        $this->addReference("countryGW", $countryGW);
        $this->addReference("countryGY", $countryGY);
        $this->addReference("countryHK", $countryHK);
        $this->addReference("countryHM", $countryHM);
        $this->addReference("countryHN", $countryHN);
        $this->addReference("countryHR", $countryHR);
        $this->addReference("countryHT", $countryHT);
        $this->addReference("countryHU", $countryHU);
        $this->addReference("countryID", $countryID);
        $this->addReference("countryIE", $countryIE);
        $this->addReference("countryIL", $countryIL);
        $this->addReference("countryIN", $countryIN);
        $this->addReference("countryIO", $countryIO);
        $this->addReference("countryIQ", $countryIQ);
        $this->addReference("countryIR", $countryIR);
        $this->addReference("countryIS", $countryIS);
        $this->addReference("countryIT", $countryIT);
        $this->addReference("countryJM", $countryJM);
        $this->addReference("countryJO", $countryJO);
        $this->addReference("countryJP", $countryJP);
        $this->addReference("countryKE", $countryKE);
        $this->addReference("countryKG", $countryKG);
        $this->addReference("countryKH", $countryKH);
        $this->addReference("countryKI", $countryKI);
        $this->addReference("countryKM", $countryKM);
        $this->addReference("countryKN", $countryKN);
        $this->addReference("countryKP", $countryKP);
        $this->addReference("countryKR", $countryKR);
        $this->addReference("countryKW", $countryKW);
        $this->addReference("countryKY", $countryKY);
        $this->addReference("countryKZ", $countryKZ);
        $this->addReference("countryLA", $countryLA);
        $this->addReference("countryLB", $countryLB);
        $this->addReference("countryLC", $countryLC);
        $this->addReference("countryLI", $countryLI);
        $this->addReference("countryLK", $countryLK);
        $this->addReference("countryLR", $countryLR);
        $this->addReference("countryLS", $countryLS);
        $this->addReference("countryLT", $countryLT);
        $this->addReference("countryLU", $countryLU);
        $this->addReference("countryLV", $countryLV);
        $this->addReference("countryLY", $countryLY);
        $this->addReference("countryMA", $countryMA);
        $this->addReference("countryMC", $countryMC);
        $this->addReference("countryMD", $countryMD);
        $this->addReference("countryMG", $countryMG);
        $this->addReference("countryMH", $countryMH);
        $this->addReference("countryMK", $countryMK);
        $this->addReference("countryML", $countryML);
        $this->addReference("countryMM", $countryMM);
        $this->addReference("countryMN", $countryMN);
        $this->addReference("countryMO", $countryMO);
        $this->addReference("countryMP", $countryMP);
        $this->addReference("countryMQ", $countryMQ);
        $this->addReference("countryMR", $countryMR);
        $this->addReference("countryMS", $countryMS);
        $this->addReference("countryMT", $countryMT);
        $this->addReference("countryMU", $countryMU);
        $this->addReference("countryMV", $countryMV);
        $this->addReference("countryMW", $countryMW);
        $this->addReference("countryMX", $countryMX);
        $this->addReference("countryMY", $countryMY);
        $this->addReference("countryMZ", $countryMZ);
        $this->addReference("countryNA", $countryNA);
        $this->addReference("countryNC", $countryNC);
        $this->addReference("countryNE", $countryNE);
        $this->addReference("countryNF", $countryNF);
        $this->addReference("countryNG", $countryNG);
        $this->addReference("countryNI", $countryNI);
        $this->addReference("countryNL", $countryNL);
        $this->addReference("countryNO", $countryNO);
        $this->addReference("countryNP", $countryNP);
        $this->addReference("countryNR", $countryNR);
        $this->addReference("countryNU", $countryNU);
        $this->addReference("countryNZ", $countryNZ);
        $this->addReference("countryOM", $countryOM);
        $this->addReference("countryPA", $countryPA);
        $this->addReference("countryPE", $countryPE);
        $this->addReference("countryPF", $countryPF);
        $this->addReference("countryPG", $countryPG);
        $this->addReference("countryPH", $countryPH);
        $this->addReference("countryPK", $countryPK);
        $this->addReference("countryPL", $countryPL);
        $this->addReference("countryPM", $countryPM);
        $this->addReference("countryPN", $countryPN);
        $this->addReference("countryPR", $countryPR);
        $this->addReference("countryPS", $countryPS);
        $this->addReference("countryPT", $countryPT);
        $this->addReference("countryPW", $countryPW);
        $this->addReference("countryPY", $countryPY);
        $this->addReference("countryQA", $countryQA);
        $this->addReference("countryRE", $countryRE);
        $this->addReference("countryRO", $countryRO);
        $this->addReference("countryRU", $countryRU);
        $this->addReference("countryRW", $countryRW);
        $this->addReference("countrySA", $countrySA);
        $this->addReference("countrySB", $countrySB);
        $this->addReference("countrySC", $countrySC);
        $this->addReference("countrySD", $countrySD);
        $this->addReference("countrySE", $countrySE);
        $this->addReference("countrySG", $countrySG);
        $this->addReference("countrySH", $countrySH);
        $this->addReference("countrySI", $countrySI);
        $this->addReference("countrySJ", $countrySJ);
        $this->addReference("countrySK", $countrySK);
        $this->addReference("countrySL", $countrySL);
        $this->addReference("countrySM", $countrySM);
        $this->addReference("countrySN", $countrySN);
        $this->addReference("countrySO", $countrySO);
        $this->addReference("countrySR", $countrySR);
        $this->addReference("countryST", $countryST);
        $this->addReference("countrySV", $countrySV);
        $this->addReference("countrySY", $countrySY);
        $this->addReference("countrySZ", $countrySZ);
        $this->addReference("countryTC", $countryTC);
        $this->addReference("countryTD", $countryTD);
        $this->addReference("countryTF", $countryTF);
        $this->addReference("countryTG", $countryTG);
        $this->addReference("countryTH", $countryTH);
        $this->addReference("countryTJ", $countryTJ);
        $this->addReference("countryTK", $countryTK);
        $this->addReference("countryTL", $countryTL);
        $this->addReference("countryTM", $countryTM);
        $this->addReference("countryTN", $countryTN);
        $this->addReference("countryTO", $countryTO);
        $this->addReference("countryTR", $countryTR);
        $this->addReference("countryTT", $countryTT);
        $this->addReference("countryTV", $countryTV);
        $this->addReference("countryTW", $countryTW);
        $this->addReference("countryTZ", $countryTZ);
        $this->addReference("countryUA", $countryUA);
        $this->addReference("countryUG", $countryUG);
        $this->addReference("countryUM", $countryUM);
        $this->addReference("countryUS", $countryUS);
        $this->addReference("countryUY", $countryUY);
        $this->addReference("countryUZ", $countryUZ);
        $this->addReference("countryVA", $countryVA);
        $this->addReference("countryVC", $countryVC);
        $this->addReference("countryVE", $countryVE);
        $this->addReference("countryVG", $countryVG);
        $this->addReference("countryVI", $countryVI);
        $this->addReference("countryVN", $countryVN);
        $this->addReference("countryVU", $countryVU);
        $this->addReference("countryWF", $countryWF);
        $this->addReference("countryWS", $countryWS);
        $this->addReference("countryYE", $countryYE);
        $this->addReference("countryYT", $countryYT);
        $this->addReference("countryZA", $countryZA);
        $this->addReference("countryZM", $countryZM);
        $this->addReference("countryZW", $countryZW);
        
    }

    public function getOrder()
    {
        return 0;
    }

}