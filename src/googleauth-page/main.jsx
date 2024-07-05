import { useEffect, useState } from 'react';
import { createRoot, render, StrictMode, createInterpolateElement } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Button, TextControl } from '@wordpress/components';
import { Notice } from '@wordpress/components';


import "./scss/style.scss"

const { __ } = wp.i18n;

const domElement = document.getElementById(window.wpmudevPluginTest.dom_element_id);

const WPMUDEV_PluginTest = () => {

    const [googleCredentials, setGoogleCredientials] = useState({})
    const [isLoading, setLoading] = useState(false);
    const [status, setStatus] = useState();
    const [statusMessage, setStatusMessage] = useState();


    const handleSaveSetting = async () => {
        try {
            setLoading(true)
            const res = await apiFetch({
                method: 'POST',
                path: window.wpmudevPluginTest.restEndpointSave,
                data: { ...googleCredentials },
            });
            setLoading(false);
            setStatus("success");
            setStatusMessage("Settings Saved Successfully");

        } catch (error) {
            setLoading(false)
            setStatus("error");
            setStatusMessage("Error saving settings");
        }

    }

    const handleChange = (name, value) => {
        setGoogleCredientials({ ...googleCredentials, [name]: value })
    }

    useEffect(() => {
        const fetchGoogleSettings = async () => {
            try {
                const res = await apiFetch({ path: window.wpmudevPluginTest.restEndpointSave });
                setGoogleCredientials(res.data);

            } catch (error) {
                setGoogleCredientials({});
            }
        }
        fetchGoogleSettings()
    }, [apiFetch]);

    const {client_id, client_secret} = googleCredentials || {};
    const isDisabled = (!client_id || client_id?.length === 0 || !client_secret || client_secret?.length === 0 )
    
    return (
        <>
            <div class="sui-header">
                <h1 class="sui-header-title">
                    {__('Settings', 'wpmudev-plugin-test')}
                </h1>
            </div>

            <div className="sui-box">

                {status && <Notice status={status} onDismiss={() => {
                    setStatus();
                    setStatusMessage()
                }}>{__(statusMessage, 'wpmudev-plugin-test')}</Notice>}

                <div className="sui-box-header">
                    <h2 className="sui-box-title"> {__('Set Google credentials', 'wpmudev-plugin-test')}</h2>
                </div>

                <div className="sui-box-body">
                    <div className="sui-box-settings-row">
                        <TextControl
                            help={createInterpolateElement(
                                __('You can get Client ID from <a>here</a>.', 'wpmudev-plugin-test'),
                                {
                                    a: <a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid" />,
                                }
                            )}
                            label={__("Client ID", 'wpmudev-plugin-test')}
                            value={googleCredentials?.client_id}
                            onChange={(val) => handleChange('client_id', val)}
                        />
                    </div>

                    <div className="sui-box-settings-row">
                        <TextControl
                            help={createInterpolateElement(
                                __('You can get Client Secret from <a>here</a>.', 'wpmudev-plugin-test'),
                                {
                                    a: <a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid" />,
                                }
                            )}
                            label={__("Client Secret", 'wpmudev-plugin-test')}
                            type="password"
                            value={googleCredentials?.client_secret}
                            onChange={(val) => handleChange('client_secret', val)}

                        />
                    </div>

                    {/* <div className="sui-box-settings-row">
                    <span>Please use this url <em>{window.wpmudevPluginTest.returnUrl}</em> in your Google API's <strong>Authorized redirect URIs</strong> field</span>
                </div>
                 */}


                </div>

                <div className="sui-box-footer">
                    <div className="sui-actions-right">
                        <Button
                            variant="primary"
                            onClick={handleSaveSetting}
                            text={__('Save', 'wpmudev-plugin-test')}
                            disabled={isLoading || isDisabled}
                        />
                    </div>
                </div>

            </div>

        </>
    );
}

if (createRoot) {
    createRoot(domElement).render(<StrictMode><WPMUDEV_PluginTest /></StrictMode>);
} else {
    render(<StrictMode><WPMUDEV_PluginTest /></StrictMode>, domElement);
}
